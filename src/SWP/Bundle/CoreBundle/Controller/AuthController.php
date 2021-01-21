<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\UserBundle\Model\UserManagerInterface;
use GuzzleHttp;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SWP\Bundle\CoreBundle\Factory\ApiKeyFactory;
use SWP\Bundle\CoreBundle\Form\Type\SuperdeskCredentialAuthenticationType;
use SWP\Bundle\CoreBundle\Form\Type\UserAuthenticationType;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepositoryInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthController extends AbstractController
{
    protected $formFactory;

    protected $apiKeyRepository;

    protected $apiKeyFactory;

    protected $lockFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        ApiKeyRepositoryInterface $apiKeyRepository,
        ApiKeyFactory $apiKeyFactory,
        Factory $lockFactory
    ) {
        $this->formFactory = $formFactory;
        $this->apiKeyRepository = $apiKeyRepository;
        $this->apiKeyFactory = $apiKeyFactory;
        $this->lockFactory = $lockFactory;
    }

    /**
     * @Route("/api/{version}/auth/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_auth")
     */
    public function authenticateAction(Request $request, UserProviderInterface $userProvider, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $form = $this->formFactory->createNamed('', UserAuthenticationType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $user = $userProvider->loadUserByUsername($formData['username']);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }

            if ((null !== $user) && $userPasswordEncoder->isPasswordValid($user, $formData['password'])) {
                return $this->returnApiTokenResponse($user);
            }
        }

        return new SingleResourceResponse([
            'status' => 401,
            'message' => 'Unauthorized',
        ], new ResponseContext(401));
    }

    /**
     * @Route("/api/{version}/auth/superdesk/", options={"expose"=true}, methods={"POST"}, defaults={"version"="v2"}, name="swp_api_auth_superdesk")
     */
    public function authenticateWithSuperdeskAction(
        Request $request,
        LoggerInterface $logger,
        array $superdeskServers,
        UserProviderInterface $userProvider,
        UserManagerInterface $userManager
    ) {
        $form = $this->formFactory->createNamed('', SuperdeskCredentialAuthenticationType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $authorizedSuperdeskHosts = $superdeskServers;
            $superdeskUser = null;
            $client = new GuzzleHttp\Client();

            foreach ($authorizedSuperdeskHosts as $baseUrl) {
                try {
                    $apiRequest = new GuzzleHttp\Psr7\Request('GET', sprintf('%s/api/sessions/%s', $baseUrl, $formData['sessionId']), [
                        'Authorization' => $formData['token'],
                    ]);

                    $apiResponse = $client->send($apiRequest);
                    if (200 !== $apiResponse->getStatusCode()) {
                        $logger->warning(sprintf('[%s] Unsuccessful response from Superdesk Server: %s', $apiResponse->getStatusCode(), $apiResponse->getBody()->getContents()));

                        continue;
                    }

                    $content = json_decode($apiResponse->getBody()->getContents(), true);
                    if (is_array($content) && array_key_exists('user', $content)) {
                        $superdeskUser = $content['user'];

                        break;
                    }
                } catch (GuzzleHttp\Exception\ClientException $e) {
                    $logger->warning(sprintf('Error when logging in Superdesk: %s', $e->getMessage()));

                    continue;
                }
            }

            if (null === $superdeskUser) {
                return new SingleResourceResponse([
                    'status' => 401,
                    'message' => <<<'MESSAGE'
Unauthorized (user not found in Superdesk). 
Make sure that Publisher can talk to Superdesk instance. Set it's address in "SUPERDESK_SERVERS" environment variable.
MESSAGE,
                ], new ResponseContext(401));
            }

            $publisherUser = $userProvider->findOneByEmail($superdeskUser['email']);
            if (null === $publisherUser) {
                try {
                    $publisherUser = $userProvider->loadUserByUsername($superdeskUser['username']);
                } catch (UsernameNotFoundException $e) {
                    $publisherUser = null;
                }
            }

            if (null === $publisherUser) {
                /** @var UserInterface $publisherUser */
                $publisherUser = $userManager->createUser();
                $publisherUser->setUsername($superdeskUser['username']);
                $publisherUser->setEmail($superdeskUser['email']);
                $publisherUser->setRoles(['ROLE_INTERNAL_API']);
                $publisherUser->setFirstName(\array_key_exists('first_name', $superdeskUser) ? $superdeskUser['first_name'] : 'Anon.');
                $publisherUser->setLastName(\array_key_exists('last_name', $superdeskUser) ? $superdeskUser['last_name'] : '');
                $publisherUser->setPassword(password_hash(random_bytes(36), PASSWORD_BCRYPT));
                $publisherUser->setEnabled(true);
                $userManager->updateUser($publisherUser);
            }

            if (null !== $publisherUser) {
                return $this->returnApiTokenResponse($publisherUser, str_replace('Basic ', '', $formData['token']));
            }
        }

        return new SingleResourceResponse([
            'status' => 401,
            'message' => 'Unauthorized',
        ], new ResponseContext(401));
    }

    private function returnApiTokenResponse(UserInterface $user, string $token = null): SingleResourceResponseInterface
    {
        /** @var ApiKeyInterface $apiKey */
        $apiKey = $this->generateOrGetApiKey($user, $token);

        return new SingleResourceResponse([
            'token' => [
                'api_key' => $apiKey->getApiKey(),
                'valid_to' => $apiKey->getValidTo(),
            ],
            'user' => $user,
        ]);
    }

    private function generateOrGetApiKey(UserInterface $user, $token): ?ApiKeyInterface
    {
        $apiKey = null;
        if (null !== $token) {
            $apiKey = $this->apiKeyRepository->getValidToken($token)->getQuery()->getOneOrNullResult();
        } else {
            $validKeys = $this->apiKeyRepository->getValidTokenForUser($user)->getQuery()->getResult();
            if (count($validKeys) > 0) {
                $apiKey = reset($validKeys);
            }
        }

        if (null === $apiKey) {
            $apiKey = $this->apiKeyFactory->create($user, $token);

            try {
                $lock = $this->lockFactory->createLock(md5(json_encode(['type' => 'user_api_key', 'user' => $user->getId()])), 2);
                if (!$lock->acquire()) {
                    throw new RuntimeException('Other api key is created right now for this user');
                }
                $this->apiKeyRepository->add($apiKey);
                $lock->release();
            } catch (RuntimeException $e) {
                sleep(2);

                return $this->generateOrGetApiKey($user, $token);
            }
        }

        return $apiKey;
    }
}
