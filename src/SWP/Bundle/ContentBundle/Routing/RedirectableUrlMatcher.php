<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */


namespace SWP\Bundle\ContentBundle\Routing;

use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseRedirectableUrlMatcher;

class RedirectableUrlMatcher extends BaseRedirectableUrlMatcher
{
    public function redirect($path, $route, $scheme = null): array
    {
        return [
            '_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction',
            'path' => $path,
            'permanent' => true,
            'scheme' => $scheme,
            'httpPort' => $this->context->getHttpPort(),
            'httpsPort' => $this->context->getHttpsPort(),
            '_route' => $route,
        ];
    }
}
