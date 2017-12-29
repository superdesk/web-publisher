<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Processor;

use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

class RequiredDataProcessor implements RequiredDataProcessorInterface
{
    /**
     * @var RouteServiceInterface
     */
    protected $routeService;

    /**
     * @var RouteRepositoryInterface
     */
    protected $routeRepository;

    /**
     * @var RouteProviderInterface
     */
    protected $routeProvider;

    /**
     * @var RouteFactoryInterface
     */
    protected $routeFactory;

    /**
     * RequiredDataProcessor constructor.
     *
     * @param RouteServiceInterface    $routeService
     * @param RouteRepositoryInterface $routeRepository
     * @param RouteProviderInterface   $routeProvider
     * @param RouteFactoryInterface    $routeFactory
     */
    public function __construct(RouteServiceInterface $routeService, RouteRepositoryInterface $routeRepository, RouteProviderInterface $routeProvider, RouteFactoryInterface $routeFactory)
    {
        $this->routeService = $routeService;
        $this->routeRepository = $routeRepository;
        $this->routeProvider = $routeProvider;
        $this->routeFactory = $routeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function processTheme(ThemeInterface $theme): void
    {
        foreach ($theme->getRoutes() as $routeData) {
            $route = $this->createRoute($routeData);
            if (null !== $this->routeProvider->getOneByStaticPrefix($route->getStaticPrefix())) {
                continue;
            }

            $this->routeRepository->add($route);
        }
    }

    /**
     * @param array $routeData
     *
     * @return RouteInterface
     */
    protected function createRoute(array $routeData): RouteInterface
    {
        /** @var RouteInterface $route */
        $route = $this->routeFactory->create();
        $route->setName($routeData['name']);
        $route->setSlug($routeData['slug']);
        $route->setType($routeData['type']);
        if (null !== $routeData['parentName'] && null !== $parent = $this->routeProvider->getRouteByName($routeData['parentName'])) {
            $route->setParent($parent);
        }
        $this->routeService->createRoute($route);

        return $route;
    }
}
