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

use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\RouteProvider as BaseRouteProvider;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\Route;

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

    /**
     * RouteProvider constructor.
     *
     * @param RouteRepositoryInterface $routeRepository
     * @param ManagerRegistry          $managerRegistry
     * @param CandidatesInterface      $candidatesStrategy
     * @param string                   $className
     */
    public function __construct(RouteRepositoryInterface $routeRepository, ManagerRegistry $managerRegistry, CandidatesInterface $candidatesStrategy, string $className)
    {
        $this->routeRepository = $routeRepository;
        $this->internalRoutesCache = [];
        $this->candidatesStrategy = $candidatesStrategy;

        parent::__construct($managerRegistry, $candidatesStrategy, $className);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $collection = new RouteCollection();

        $candidates = $this->candidatesStrategy->getCandidates($request);
        if (0 === count($candidates)) {
            return $collection;
        }
        // As we use Gedmo Sortable on position field, we need to reverse sorting to get child routes first
        $routes = $this->getRouteRepository()->findByStaticPrefix($candidates, ['level' => 'DESC', 'position' => 'DESC']);

        /** @var $route Route */
        foreach ($routes as $route) {
            $collection->add($route->getName(), $route);
        }

        return $collection;
    }

    /**
     * @return RouteRepositoryInterface
     */
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
