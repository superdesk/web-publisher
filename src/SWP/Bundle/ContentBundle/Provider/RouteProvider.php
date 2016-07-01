<?php

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

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
        return $this->routeRepository->find($this->pathBuilder->build($this->basePaths[0].'/'.rtrim(ltrim($id))));
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteForArticle(ArticleInterface $article)
    {
        return $this->routeRepository->find(
            $this->pathBuilder->build($this->basePaths[0].'/'.rtrim(ltrim($this->defaultPath)))
        );
    }
}
