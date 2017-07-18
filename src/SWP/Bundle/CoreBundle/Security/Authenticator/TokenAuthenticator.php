<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use SWP\Bundle\CoreBundle\EventListener\ActivateLivesiteEditorListener;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface as CoreUserInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    const INTENTION_LIVESITE_EDITOR = 'livesite_editor';

    /**
     * @var ApiKeyRepository
     */
    protected $apiKeyRepository;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var TenantRepositoryInterface
     */
    protected $tenantRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * TokenAuthenticator constructor.
     *
     * @param ApiKeyRepository          $apiKeyRepository
     * @param TenantContextInterface    $tenantContext
     * @param TenantRepositoryInterface $tenantRepository
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        ApiKeyRepository $apiKeyRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if (!$token = $this->getToken($request)) {
            // no token? Return null and no other methods will be called
            return;
        }

        $data = [
            'token' => $token,
        ];

        if (self::INTENTION_LIVESITE_EDITOR === $this->getIntention($request)) {
            $data['intention'] = self::INTENTION_LIVESITE_EDITOR;
        }

        // What you return here will be passed to getUser() as $credentials
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        /** @var ApiKeyInterface $apiKey */
        $apiKey = $this->apiKeyRepository
            ->getValidToken(str_replace('Basic ', '', $credentials['token']))
            ->getQuery()
            ->getOneOrNullResult();

        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);

        if (null === $apiKey) {
            return;
        }

        // extend valid time after login
        $apiKey->extendValidTo();

        /** @var CoreUserInterface $user */
        $user = $apiKey->getUser();
        $user->addRole('ROLE_INTERNAL_API');
        $this->apiKeyRepository->flush();

        if (array_key_exists('intention', $credentials) && self::INTENTION_LIVESITE_EDITOR === $credentials['intention']) {
            $user->addRole('ROLE_LIVESITE_EDITOR');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user instanceof CoreUserInterface) {
            $currentOrganization = $this->tenantContext->getTenant()->getOrganization();
            $userOrganization = $user->getOrganization();

            if ($currentOrganization->getId() === $userOrganization->getId()) {
                return true;
            }
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if (self::INTENTION_LIVESITE_EDITOR === $this->getIntention($request)) {
            $request->attributes->set(ActivateLivesiteEditorListener::ACTIVATION_KEY, $this->getToken($request));
        }

        // on success, let the request continue
        return;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'status' => 403,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'status' => 401,
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function getIntention(Request $request)
    {
        return $request->headers->get('Intention', $request->query->get('intention'));
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getToken(Request $request)
    {
        return $request->headers->get('Authorization', $request->query->get('auth_token'));
    }
}
