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

use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RouteService implements RouteServiceInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RouteService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute(RouteInterface $route)
    {
        $this->dispatchRouteEvent(RouteEvents::PRE_CREATE, $route);

        $route = $this->fillRoute($route);

        $this->dispatchRouteEvent(RouteEvents::POST_CREATE, $route);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRoute(RouteInterface $route)
    {
        $this->dispatchRouteEvent(RouteEvents::PRE_UPDATE, $route);

        $route = $this->fillRoute($route);

        $this->dispatchRouteEvent(RouteEvents::POST_UPDATE, $route);

        return $route;
    }

    private function dispatchRouteEvent($eventName, RouteInterface $route)
    {
        $this->eventDispatcher->dispatch($eventName, new RouteEvent($route));
    }

    /**
     * @param RouteInterface $route
     *
     * @return RouteInterface
     */
    public function fillRoute(RouteInterface $route)
    {
        switch ($route->getType()) {
            case RouteInterface::TYPE_CONTENT:
                $route->setVariablePattern(null);
                $route->setStaticPrefix($this->generatePath($route));
                $route->setRequirements([]);

                break;
            case RouteInterface::TYPE_COLLECTION:
                $route->setVariablePattern('/{slug}');
                $route->setStaticPrefix($this->generatePath($route));
                $route->setRequirement('slug', '[a-zA-Z0-9\-_\/]+');
                $route->setDefault('slug', null);

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Route type "%s" is unsupported!', $route->getType()));
        }

        return $route;
    }

    /**
     * @param RouteInterface $route
     *
     * @return string
     */
    protected function generatePath(RouteInterface $route)
    {
        if (null === $parent = $route->getParent()) {
            return '/'.$route->getName();
        }

        return sprintf('%s/%s', $parent->getStaticPrefix(), $route->getName());
    }
}
