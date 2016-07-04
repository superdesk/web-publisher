<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RouteService implements RouteServiceInterface
{
    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * @var ArticleProviderInterface
     */
    private $articleProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RouteService constructor.
     *
     * @param RouteFactoryInterface    $routeFactory
     * @param RouteProviderInterface   $routeProvider
     * @param ArticleProviderInterface $articleProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RouteFactoryInterface $routeFactory,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->routeFactory = $routeFactory;
        $this->routeProvider = $routeProvider;
        $this->articleProvider = $articleProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute(array $routeData)
    {
        $route = $this->routeFactory->create();

        $this->dispatchRouteEvent(RouteEvents::PRE_CREATE, $route);

        $route = $this->fillRoute($route, $routeData);

        $this->dispatchRouteEvent(RouteEvents::POST_CREATE, $route);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRoute(RouteInterface $route, array $routeData)
    {
        $this->dispatchRouteEvent(RouteEvents::PRE_UPDATE, $route);

        $route = $this->fillRoute($route, $routeData);

        $this->dispatchRouteEvent(RouteEvents::POST_UPDATE, $route);

        return $route;
    }

    private function dispatchRouteEvent($eventName, RouteInterface $route)
    {
        $this->eventDispatcher->dispatch($eventName, new RouteEvent($route));
    }

    private function fillRoute(RouteInterface $route, array $routeData)
    {
        if (isset($routeData['parent'])) {
            if (!is_null($routeData['parent']) && $routeData['parent'] !== '/') {
                $parentRoute = $this->routeProvider->getOneById($routeData['parent']);

                if (null !== $parentRoute) {
                    $route->setParentDocument($parentRoute);
                }
            } else {
                $route->setParentDocument($this->routeProvider->getBaseRoute());
            }
        }

        if (isset($routeData['content']) && !is_null($routeData['content'])) {
            $article = $this->articleProvider->findOneById($routeData['content']);

            if (null !== $article) {
                $route->setContent($article);
            }
        }

        if (isset($routeData['name'])) {
            $route->setName($routeData['name']);
        }

        if (isset($routeData['type']) && $routeData['type'] == RouteInterface::TYPE_CONTENT) {
            $route->setDefault('_controller', '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction');
            $route->setVariablePattern(null);
            $route->setRequirements([]);
        } elseif (isset($routeData['type'])) {
            $route->setDefault('_controller', '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction');
            $route->setVariablePattern('/{slug}');
            $route->setRequirement('slug', '[a-zA-Z1-9\-_\/]+');
        }

        return $route;
    }
}
