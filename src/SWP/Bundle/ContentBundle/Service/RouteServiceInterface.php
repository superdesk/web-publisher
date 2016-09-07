<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface RouteServiceInterface
{
    /**
     * Creates a new route.
     *
     * @param array $routeData
     *
     * @return RouteInterface
     */
    public function createRoute(array $routeData);

    /**
     * Updates existing route.
     *
     * @param RouteObjectInterface $route
     * @param array                $routeData
     *
     * @return RouteInterface
     */
    public function updateRoute(RouteObjectInterface $route, array $routeData);
}
