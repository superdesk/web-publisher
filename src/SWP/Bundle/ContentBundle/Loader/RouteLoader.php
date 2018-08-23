<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentBundle\Loader;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

/**
 * Class RouteLoader.
 */
class RouteLoader extends PaginatedLoader implements LoaderInterface
{
    const SUPPORTED_TYPE = 'route';

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * @var RouteRepositoryInterface
     */
    protected $routeRepository;

    /**
     * @var array
     */
    protected $supportedParameters = ['name', 'slug', 'parent'];

    /**
     * RouteLoader constructor.
     *
     * @param MetaFactoryInterface     $metaFactory
     * @param RouteRepositoryInterface $routeRepository
     */
    public function __construct(MetaFactoryInterface $metaFactory, RouteRepositoryInterface $routeRepository)
    {
        $this->metaFactory = $metaFactory;
        $this->routeRepository = $routeRepository;
    }

    /**
     *  {@inheritdoc}
     */
    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if (LoaderInterface::SINGLE === $responseType) {
            $route = isset($parameters['route_object']) ? $parameters['route_object'] : null;
            if (null === $route) {
                if (empty($parameters)) {
                    return false;
                }

                $criteria = new Criteria();
                foreach ($this->supportedParameters as $supportedParameter) {
                    if (array_key_exists($supportedParameter, $parameters)) {
                        $criteria->set($supportedParameter, $parameters[$supportedParameter]);
                    }
                }

                $route = $this->routeRepository->getQueryByCriteria($criteria, [], 'r')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            if (null !== $route) {
                return $this->metaFactory->create($route);
            }
        } elseif (LoaderInterface::COLLECTION === $responseType) {
            $criteria = new Criteria();

            if (\array_key_exists('parent', $parameters)) {
                $parent = $parameters['parent'];
                if (is_numeric($parent)) {
                    $criteria->set('parent', $parent);
                } elseif (\is_string($parent)) {
                    $parentObject = $this->routeRepository->getQueryByCriteria(new Criteria(['name' => $parent]), $criteria->get('order', []), 'r')
                        ->getQuery()->getOneOrNullResult();
                    if (null !== $parentObject) {
                        $criteria->set('parent', $parentObject->getId());
                    }
                } elseif ($parent instanceof Meta && $parent->getValues() instanceof RouteInterface) {
                    $criteria->set('parent', $parent->getValues()->getId());
                }
            }

            $this->applyPaginationToCriteria($criteria, $parameters);
            $countCriteria = clone $criteria;
            $routesCollection = $this->routeRepository->getQueryByCriteria($criteria, $criteria->get('order', []), 'r')->getQuery()->getResult();

            if (\count($routesCollection) > 0) {
                $metaCollection = new MetaCollection();
                $metaCollection->setTotalItemsCount($this->routeRepository->countByCriteria($countCriteria));
                foreach ($routesCollection as $route) {
                    $routeMeta = $this->metaFactory->create($route);
                    if (null !== $routeMeta) {
                        $metaCollection->add($routeMeta);
                    }
                }
                unset($routesCollection, $route, $criteria);

                return $metaCollection;
            }
        }

        return false;
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported(string $type): bool
    {
        return self::SUPPORTED_TYPE === $type;
    }
}
