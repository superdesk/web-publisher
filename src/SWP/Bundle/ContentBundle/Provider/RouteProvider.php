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

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $routeRepository;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    private $pathBuilder;

    /**
     * @var array
     */
    private $basePaths;

    /**
     * @var string
     */
    private $defaultPath;

    /**
     * RouteProvider constructor.
     *
     * @param RepositoryInterface             $routeRepository Route repository
     * @param TenantAwarePathBuilderInterface $pathBuilder     Path builder
     * @param array                           $basePaths       Tenant's path under which all other routes are stored
     * @param string                          $defaultPath     Default path under which the articles are stored
     */
    public function __construct(
        RepositoryInterface $routeRepository,
        TenantAwarePathBuilderInterface $pathBuilder,
        array $basePaths,
        $defaultPath
    ) {
        $this->routeRepository = $routeRepository;
        $this->pathBuilder = $pathBuilder;
        $this->basePaths = $basePaths;
        $this->defaultPath = $defaultPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRoute()
    {
        return $this->routeRepository->find($this->pathBuilder->build($this->basePaths[0]));
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        $basePath = $this->pathBuilder->build($this->basePaths[0]);
        $id = ltrim(str_replace($basePath, '', $id), '/');

        if ('' === $id) {
            return $this->getBaseRoute();
        }

        return $this->routeRepository->find($this->pathBuilder->build($this->basePaths[0].'/'.$id));
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteForArticle(ArticleInterface $article)
    {
        $routeId = $this->pathBuilder->build($this->basePaths[0].'/'.$this->defaultPath);
        $route = $this->routeRepository->find($routeId);

        if (null === $route) {
            throw new RouteNotFoundException(sprintf('No route found: %s', $routeId));
        }

        return $route;
    }
}
