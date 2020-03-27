<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\RedirectRouteBundle\Model\RedirectRoute as BaseRedirectRoute;

class RedirectRoute extends BaseRedirectRoute implements RedirectRouteInterface
{
    /** @var RouteInterface|null */
    protected $routeSource;

    public function getRouteSource(): ?RouteInterface
    {
        return $this->routeSource;
    }

    public function setRouteSource(?RouteInterface $route): void
    {
        $this->routeSource = $route;
    }
}
