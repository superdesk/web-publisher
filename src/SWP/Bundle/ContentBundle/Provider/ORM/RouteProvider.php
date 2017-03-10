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

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\RouteProvider as BaseRouteProvider;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;

class RouteProvider extends BaseRouteProvider implements RouteProviderInterface
{
    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    /**
     * @var array
     */
    private $internalRoutesCache;

    /**
     * @var CandidatesInterface
     */
    private $candidatesStrategy;

    public function __construct(
        RouteRepositoryInterface $routeRepository,
        ManagerRegistry $managerRegistry,
        CandidatesInterface $candidatesStrategy,
        $className
    ) {
        $this->routeRepository = $routeRepository;
        $this->internalRoutesCache = [];
        $this->candidatesStrategy = $candidatesStrategy;

        parent::__construct($managerRegistry, $candidatesStrategy, $className);
    }

    public function getRepository(): RouteRepositoryInterface
    {
        return $this->routeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRoute()
    {
        throw new \Exception('Not implemented in ORM');
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        return $this->routeRepository->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByStaticPrefix($staticPrefix)
    {
        return $this->routeRepository->findOneBy(['staticPrefix' => $staticPrefix]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteForArticle(ArticleInterface $article)
    {
        return $article->getRoute();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteByName($name)
    {
        if (array_key_exists($name, $this->internalRoutesCache)) {
            return $this->internalRoutesCache[$name];
        }

        if (!$this->candidatesStrategy->isCandidate($name)) {
            throw new RouteNotFoundException(sprintf('Route "%s" is not handled by this route provider', $name));
        }

        $route = $this->getRouteRepository()->findOneBy(array('name' => $name));
        $this->internalRoutesCache[$name] = $route;
        if (!$route) {
            throw new RouteNotFoundException("No route found for name '$name'");
        }

        return $route;
    }
}
