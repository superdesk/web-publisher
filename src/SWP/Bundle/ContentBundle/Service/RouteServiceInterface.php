<?php

/*
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

use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface RouteServiceInterface
{
    public function createRoute(RouteInterface $route);

    public function updateRoute(RouteInterface $previousRoute, RouteInterface $route): RouteInterface;

    public function fillRoute(RouteInterface $route): RouteInterface;
}
