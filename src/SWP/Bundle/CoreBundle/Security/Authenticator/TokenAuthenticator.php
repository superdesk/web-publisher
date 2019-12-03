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

use function stripslashes;
use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface as CoreUserInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
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
     * @var Security
     */
    protected $security;

    public function __construct(
        ApiKeyRepository $apiKeyRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository,
        EventDispatcherInterface $eventDispatcher,
        Security $security
    ) {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->security = $security;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     *
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $this->getToken($request),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        /** @var ApiKeyInterface $apiKey */
        $apiKey = $this->apiKeyRepository
            ->getValidToken(str_replace('Basic ', '', stripslashes($credentials['token'])))
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

        return $user;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'status' => 403,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, 403);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'status' => 401,
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, 401);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        if ($this->security->getUser() || !$token = $this->getToken($request)) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getToken(Request $request)
    {
        // Check query first in case Authorization header used for Basic Auth
        return $request->query->get('auth_token', $request->headers->get('Authorization'));
    }
}
