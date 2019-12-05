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

namespace SWP\Bundle\CoreBundle\Security\Authenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class PreviewTokenAuthenticator extends TokenAuthenticator
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        throw new AccessDeniedHttpException(strtr($exception->getMessageKey(), $exception->getMessageData()));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        throw new UnauthorizedHttpException('Authentication Required');
    }
}
