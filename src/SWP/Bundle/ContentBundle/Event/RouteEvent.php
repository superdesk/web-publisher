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

namespace SWP\Bundle\ContentBundle\Event;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RouteEvent extends Event
{
    /**
     * @var RouteInterface
     */
    protected $route;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * RouteEvent constructor.
     *
     * @param RouteInterface $route
     * @param string         $eventName
     */
    public function __construct(RouteInterface $route, $eventName)
    {
        $this->route = $route;
        $this->eventName = $eventName;
    }

    /**
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }
}
