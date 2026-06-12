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

use SWP\Bundle\CoreBundle\Model\ApiKeyInterface;
use SWP\Bundle\CoreBundle\Model\UserInterface as CoreUserInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use function stripslashes;

class TokenAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
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

    public function supports(Request $request): ?bool
    {
        $token = $this->getToken($request);
        $isApi = $request->attributes->get('_fos_rest_zone');

        if (false === $isApi && $request->query->has('auth_token')) {
            return true;
        }

        if (false === $isApi) {
            return false;
        }

        return null !== $token && false === strpos($token, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $this->getToken($request);
        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        return new SelfValidatingPassport(new UserBadge($token, function (string $token) {
            $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

            /** @var ApiKeyInterface|null $apiKey */
            $apiKey = $this->apiKeyRepository
                ->getValidToken(str_replace('Basic ', '', stripslashes($token)))
                ->getQuery()
                ->getOneOrNullResult();
            $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);

            if (null === $apiKey) {
                throw new CustomUserMessageAuthenticationException('Invalid API token.');
            }

            // extend valid time after login
            $apiKey->extendValidTo();

            /** @var CoreUserInterface $user */
            $user = $apiKey->getUser();

            $currentOrganization = $this->tenantContext->getTenant()->getOrganization();
            $userOrganization = $user->getOrganization();
            if ($currentOrganization->getId() !== $userOrganization->getId()) {
                throw new CustomUserMessageAuthenticationException('User does not belong to the current organization.');
            }

            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
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

    private function getToken(Request $request): ?string
    {
        return $request->query->get('auth_token', $request->headers->get('Authorization', null));
    }
}
