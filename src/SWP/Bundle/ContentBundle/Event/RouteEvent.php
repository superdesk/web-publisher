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

namespace SWP\Bundle\ContentBundle\Event;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\EventDispatcher\Event;

class RouteEvent extends Event
{
    /**
     * @var RouteInterface
     */
    protected $route;

    /**
     * RouteEvent constructor.
     *
     * @param RouteInterface $route
     */
    public function __construct(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }
}
