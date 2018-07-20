<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\Faker\Provider;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;

final class RouteProvider
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    public function __construct(RouteProviderInterface $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }

    public function getRouteByName(string $id): RouteInterface
    {
        return $this->routeProvider->getRouteByName($id);
    }

    public function getRouteIdByName(string $id): ?RouteInterface
    {
        $route = $this->routeProvider->getRouteByName($id);

        if (null !== $route) {
            return null;
        }

        return $route->getId();
    }
}
