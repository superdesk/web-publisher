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

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
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

    /** @var RepositoryInterface */
    private redirectRouteRepository;

    public function __construct(RouteRepositoryInterface $routeRepository, ManagerRegistry $managerRegistry, CandidatesInterface $candidatesStrategy, string $className, RepositoryInterface $redirectRouteRepository)
    {
        $this->routeRepository = $routeRepository;
        $this->internalRoutesCache = [];
        $this->candidatesStrategy = $candidatesStrategy;
        $this->redirectRouteRepository = $redirectRouteRepository;

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

        $redirectRoute = $this->redirectRouteRepository->findOneBy(['staticPrefix' => $candidates[0]]);

        if (null !== $redirectRoute) {
            $collection->add($redirectRoute->getRouteName(), $redirectRoute);

            return $collection;
        }
        // As we use Gedmo Sortable on position field, we need to reverse sorting to get child routes first
        $routes = $this->getByStaticPrefix($candidates, ['level' => 'DESC', 'position' => 'DESC']);

        /** @var $route Route */
        foreach ($routes as $route) {
            if ($route instanceof RedirectRouteInterface) {
                $collection->add($route->getRouteName(), $route);
            } else {
                $collection->add($route->getName(), $route);
            }
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
    public function getOneByName(string $name)
    {
        return $this->routeRepository->findOneBy(['name' => $name]);
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
    public function getByStaticPrefix(array $candidates, array $orderBy = []): array
    {
        return $this->getRouteRepository()->findBy(['staticPrefix' => $candidates], $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenByStaticPrefix(array $candidates, array $orderBy = []): array
    {
        return $this->getRepository()->getChildrenByStaticPrefix($candidates, $orderBy)->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getWithChildrenByStaticPrefix(array $candidates): ?array
    {
        $routes = null;
        $routesForChilldrensLoading = [];
        foreach ($candidates as $key => $providedRoute) {
            if (false !== strpos($providedRoute, '/*')) {
                $cleanRouteName = str_replace('/*', '', $providedRoute);
                $routesForChilldrensLoading[$cleanRouteName] = null;
                $candidates[$key] = $cleanRouteName;
            }
        }

        $routesArray = $this->getByStaticPrefix($candidates);
        if (count($routesArray) <= 0) {
            return null;
        }

        $routes = $this->getArrayOfIdsFromRoutesArray($routesArray);

        if (count($routesForChilldrensLoading) > 0) {
            foreach ($routesArray as $key => $element) {
                if (array_key_exists($element->getStaticPrefix(), $routesForChilldrensLoading)) {
                    $routesForChilldrensLoading[$element->getStaticPrefix()] = $element->getId();
                }
            }

            $routesForChilldrensLoading = array_filter(array_values($routesForChilldrensLoading));
            $childrenRoutesArray = $this->getChildrenByStaticPrefix($routesForChilldrensLoading);
            if (count($childrenRoutesArray) > 0) {
                $routes = array_merge($routes, $this->getArrayOfIdsFromRoutesArray($childrenRoutesArray));
            }
        }

        return $routes;
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

    /**
     * {@inheritdoc}
     */
    public function getByMixed($routeData)
    {
        if ($routeData instanceof Meta && $routeData->getValues() instanceof RouteInterface) {
            return $routeData->getValues();
        }

        $route = null;
        if (is_int($routeData)) {
            $route = $this->getOneById($routeData);
        } elseif (is_string($routeData)) {
            if (false !== strpos($routeData, '/')) {
                $route = $this->getOneByStaticPrefix($routeData);
            } else {
                $route = $this->getRouteByName($routeData);
            }
        } elseif (is_array($routeData)) {
            $loadByStaticPrefix = true;
            foreach ($routeData as $key => $providedRoute) {
                if (\is_numeric($providedRoute)) {
                    $loadByStaticPrefix = false;

                    break;
                }
            }

            if ($loadByStaticPrefix) {
                $route = $this->getWithChildrenByStaticPrefix($routeData);
            } else {
                $route = $routeData;
            }
        }

        return $route;
    }

    /**
     * @param array $routes
     *
     * @return array
     */
    private function getArrayOfIdsFromRoutesArray(array $routes): array
    {
        return array_map(function ($route) {
            return $route->getId();
        }, $routes);
    }
}
