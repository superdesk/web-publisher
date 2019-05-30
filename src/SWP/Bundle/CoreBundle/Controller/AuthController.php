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

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use GuzzleHttp;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\CoreBundle\Form\Type\SuperdeskCredentialAuthenticationType;
use SWP\Bundle\CoreBundle\Form\Type\UserAuthenticationType;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthController extends Controller
{
    /**
     * @Operation(
     *     tags={"auth"},
     *     summary="Look for user matching provided credentials",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
     *         @SWG\Schema(
     *             ref=@Model(type=UserAuthenticationType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\User::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="No user found or not authorized."
     *     )
     * )
     *
     * @Route("/api/{version}/auth/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_auth")
     */
    public function authenticateAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', UserAuthenticationType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            try {
                $user = $this->get('swp.security.user_provider')->loadUserByUsername($formData['username']);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }

            if (null !== $user) {
                if ($this->get('security.password_encoder')->isPasswordValid($user, $formData['password'])) {
                    return $this->returnApiTokenResponse($user, null);
                }
            }
        }

        return new SingleResourceResponse([
            'status' => 401,
            'message' => 'Unauthorized',
        ], new ResponseContext(401));
    }

    /**
     * @Operation(
     *     tags={"auth"},
     *     summary="Authorize using Superdesk credentials",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
     *         @SWG\Schema(
     *             ref=@Model(type=SuperdeskCredentialAuthenticationType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\User::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="No user found or not authorized."
     *     )
     * )
     *
     * @Route("/api/{version}/auth/superdesk/", options={"expose"=true}, methods={"POST"}, defaults={"version"="v2"}, name="swp_api_auth_superdesk")
     */
    public function authenticateWithSuperdeskAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', SuperdeskCredentialAuthenticationType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $authorizedSuperdeskHosts = (array) $this->container->getParameter('superdesk_servers');
            $superdeskUser = null;
            $client = new GuzzleHttp\Client();

            foreach ($authorizedSuperdeskHosts as $baseUrl) {
                try {
                    $apiRequest = new GuzzleHttp\Psr7\Request('GET', sprintf('%s/api/sessions/%s', $baseUrl, $formData['sessionId']), [
                        'Authorization' => $formData['token'],
                    ]);
                    $apiResponse = $client->send($apiRequest);
                    if (200 !== $apiResponse->getStatusCode()) {
                        continue;
                    }

                    $content = json_decode($apiResponse->getBody()->getContents(), true);
                    if (is_array($content) && array_key_exists('user', $content)) {
                        $superdeskUser = $content['user'];

                        break;
                    }
                } catch (GuzzleHttp\Exception\ClientException $e) {
                    if (200 !== $e->getResponse()->getStatusCode()) {
                        continue;
                    }
                }
            }

            if (null === $superdeskUser) {
                return new SingleResourceResponse([
                    'status' => 401,
                    'message' => 'Unauthorized (user not found in Superdesk)',
                ], new ResponseContext(401));
            }

            $userProvider = $this->get('swp.security.user_provider');
            $publisherUser = $userProvider->findOneByEmail($superdeskUser['email']);
            if (null === $publisherUser) {
                try {
                    $publisherUser = $userProvider->loadUserByUsername($superdeskUser['username']);
                } catch (UsernameNotFoundException $e) {
                    $publisherUser = null;
                }
            }

            if (null === $publisherUser) {
                $userManager = $this->get('fos_user.user_manager');
                /** @var UserInterface $publisherUser */
                $publisherUser = $userManager->createUser();
                $publisherUser->setUsername($superdeskUser['username']);
                $publisherUser->setEmail($superdeskUser['email']);
                $publisherUser->setRoles(['ROLE_INTERNAL_API']);
                $publisherUser->setFirstName(\array_key_exists('first_name', $superdeskUser) ? $superdeskUser['first_name'] : 'Anon.');
                $publisherUser->setLastName(\array_key_exists('last_name', $superdeskUser) ? $superdeskUser['last_name'] : '');
                $publisherUser->setPlainPassword(password_hash(random_bytes(36), PASSWORD_BCRYPT));
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

    private function generateOrGetApiKey(UserInterface $user, $token)
    {
        $apiKey = null;
        $apiKeyRepository = $this->get('swp.repository.api_key');
        if (null !== $token) {
            $apiKey = $apiKeyRepository->getValidToken($token)->getQuery()->getOneOrNullResult();
        } else {
            $validKeys = $apiKeyRepository->getValidTokenForUser($user)->getQuery()->getResult();
            if (count($validKeys) > 0) {
                $apiKey = reset($validKeys);
            }
        }

        if (null === $apiKey) {
            $apiKey = $this->get('swp.factory.api_key')->create($user, $token);

            try {
                $apiKeyRepository->add($apiKey);
            } catch (UniqueConstraintViolationException $e) {
                return $this->generateOrGetApiKey($user, $token);
            }
        }

        return $apiKey;
    }
}
