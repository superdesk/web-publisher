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

use Behat\Transliterator\Transliterator;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use SWP\Bundle\StorageBundle\Doctrine\ORM\NestedTreeEntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RouteService.
 */
class RouteService implements RouteServiceInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher, RouteRepositoryInterface $routeRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->routeRepository = $routeRepository;
    }

    public function createRoute(RouteInterface $route)
    {
        $this->dispatchRouteEvent(RouteEvents::PRE_CREATE, $route);

        $route = $this->fillRoute($route);

        $this->dispatchRouteEvent(RouteEvents::POST_CREATE, $route);

        return $route;
    }

    public function updateRoute(RouteInterface $previousRoute, RouteInterface $route): RouteInterface
    {
        $this->dispatchRouteEvent(RouteEvents::PRE_UPDATE, $route);

        $route = $this->fillRoute($route);
        if ($previousRoute->getParent() !== $route->getParent()) {
            $this->changePositionInTree($previousRoute, $route, -1);
        } else {
            $this->changePositionInTree($previousRoute, $route);
        }

        $this->dispatchRouteEvent(RouteEvents::POST_UPDATE, $route);

        return $route;
    }

    public function fillRoute(RouteInterface $route): RouteInterface
    {
        if (null === $route->getSlug()) {
            $route->setSlug(Transliterator::urlize($route->getName()));
        }

        switch ($route->getType()) {
            case RouteInterface::TYPE_CONTENT:
                $route->setVariablePattern(null);
                $route->setStaticPrefix($this->generatePath($route));
                $route->setRequirements([]);

                break;
            case RouteInterface::TYPE_COLLECTION:
                $route->setVariablePattern('/{slug}');
                $route->setStaticPrefix($this->generatePath($route));
                $route->setRequirement('slug', '[a-zA-Z0-9*\-_]+');
                $route->setDefault('slug', null);

                break;
            case RouteInterface::TYPE_CUSTOM:
                $route->setStaticPrefix($this->generatePath($route));

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Route type "%s" is unsupported!', $route->getType()));
        }

        return $route;
    }

    protected function changePositionInTree(RouteInterface $previousRoute, RouteInterface $route, int $position = null): void
    {
        if ($this->routeRepository instanceof NestedTreeEntityRepository) {
            if (null !== $position) {
                $this->persistAsLastChild($route);
            }

            if ($previousRoute->getPosition() === $route->getPosition() && null !== $position) {
                $route->setPosition($position);
            } else {
                if ($route->getPosition() > 0) {
                    $previousChild = $this->routeRepository->findOneBy(
                        ['position' => $route->getPosition() - 1, 'parent' => $route->getParent()]
                    );
                    if (null !== $previousChild) {
                        $this->routeRepository->persistAsNextSiblingOf($route, $previousChild);
                    } else {
                        $this->persistAsLastChild($route);
                    }
                } else {
                    if (null !== $route->getParent()) {
                        $this->routeRepository->persistAsFirstChildOf($route, $route->getParent());
                    } else {
                        $this->routeRepository->persistAsFirstChild($route);
                    }
                }
            }
        }
    }

    private function persistAsLastChild(RouteInterface $route): void
    {
        if (null !== $route->getParent()) {
            $this->routeRepository->persistAsLastChildOf($route, $route->getParent());
        } else {
            $this->routeRepository->persistAsLastChild($route);
        }
    }

    /**
     * @param RouteInterface $route
     *
     * @return string
     */
    protected function generatePath(RouteInterface $route)
    {
        $slug = $route->getSlug();

        if (null === $parent = $route->getParent()) {
            return '/'.$slug;
        }

        return sprintf('%s/%s', $parent->getStaticPrefix(), $slug);
    }

    /**
     * @param string         $eventName
     * @param RouteInterface $route
     */
    private function dispatchRouteEvent($eventName, RouteInterface $route)
    {
        $this->eventDispatcher->dispatch(new RouteEvent($route, $eventName),  $eventName);
    }
}
