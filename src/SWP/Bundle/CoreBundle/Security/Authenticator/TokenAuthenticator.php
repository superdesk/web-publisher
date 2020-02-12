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

use SWP\Bundle\CoreBundle\Model\User;
use Symfony\Component\HttpFoundation\Response;
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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    protected $apiKeyRepository;

    protected $tenantContext;

    protected $tenantRepository;

    protected $eventDispatcher;

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

    public function getCredentials(Request $request): array
    {
        return [
            'token' => $this->getToken($request),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        /** @var ApiKeyInterface $apiKey */
        $apiKey = $this->apiKeyRepository
            ->getValidToken(str_replace('Basic ', '', stripslashes($credentials['token'])))
            ->getQuery()
            ->getOneOrNullResult();
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);

        if (null === $apiKey) {
            return null;
        }

        // extend valid time after login
        $apiKey->extendValidTo();

        /** @var CoreUserInterface $user */
        $user = $apiKey->getUser();

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
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

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            'status' => Response::HTTP_FORBIDDEN,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function supports(Request $request): bool
    {
        if (!$request->attributes->get('_fos_rest_zone')) {
            return false;
        }

        $token = $this->getToken($request);

        return null !== $token && false === strpos($token, 'Bearer ');
    }

    private function getToken(Request $request): ?string
    {
        return $request->query->get('auth_token', $request->headers->get('Authorization', null));
    }
}
