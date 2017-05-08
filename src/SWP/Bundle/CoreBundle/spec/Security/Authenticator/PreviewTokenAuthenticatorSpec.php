<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Security\Authenticator;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Bundle\CoreBundle\Security\Authenticator\PreviewTokenAuthenticator;
use SWP\Bundle\CoreBundle\Security\Authenticator\TokenAuthenticator;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class PreviewTokenAuthenticatorSpec extends ObjectBehavior
{
    public function let(
        ApiKeyRepository $apiKeyRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($apiKeyRepository, $tenantContext, $tenantRepository, $eventDispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PreviewTokenAuthenticator::class);
    }

    public function it_extends_default_authenticator()
    {
        $this->shouldHaveType(TokenAuthenticator::class);
    }

    public function it_throws_exception_on_authentication_failure(
        Request $request,
        AuthenticationException $exception
    ) {
        $exception->getMessageKey()->willReturn('Username could not be found.');
        $exception->getMessageData()->willReturn([]);

        $this->shouldThrow(AccessDeniedHttpException::class)
            ->duringOnAuthenticationFailure($request, $exception);
    }

    public function it_throws_exception_on_authentication_start(
        Request $request,
        AuthenticationException $exception
    ) {
        $this->shouldThrow(UnauthorizedHttpException::class)
            ->duringStart($request, $exception);
    }
}
