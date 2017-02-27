<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

trait RouteAwareTrait
{
    /**
     * @var RouteInterface|null
     */
    protected $route;

    /**
     * @return RouteInterface|null
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route = null)
    {
        $this->route = $route;
    }

    /**
     * @return int|null
     */
    public function getRouteId()
    {
        if (null !== $this->route) {
            return $this->route->getId();
        }
    }
}
