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

namespace SWP\Bundle\CoreBundle\Rule;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Factory\PublishDestinationFactoryInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class PublishDestinationResolver implements PublishDestinationResolverInterface
{
    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    /**
     * @var PublishDestinationFactoryInterface
     */
    private $publishDestinationFactory;

    /**
     * PublishDestinationResolver constructor.
     *
     * @param TenantRepositoryInterface          $tenantRepository
     * @param RouteRepositoryInterface           $routeRepository
     * @param PublishDestinationFactoryInterface $publishDestinationFactory
     */
    public function __construct(
        TenantRepositoryInterface $tenantRepository,
        RouteRepositoryInterface $routeRepository,
        PublishDestinationFactoryInterface $publishDestinationFactory
    ) {
        $this->tenantRepository = $tenantRepository;
        $this->routeRepository = $routeRepository;
        $this->publishDestinationFactory = $publishDestinationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $tenant, int $route)
    {
        $tenant = $this->findTenantByCodeOrThrowException($tenant);
        $route = $this->findRouteByIdOrThrowException($route);

        return $this->publishDestinationFactory->createWithTenantAndRoute($tenant, $route);
    }

    private function findTenantByCodeOrThrowException(string $code)
    {
        if (!($tenant = $this->tenantRepository->findOneByCode($code)) instanceof TenantInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($tenant) ? get_class($tenant) : gettype($tenant),
                TenantInterface::class);
        }

        return $tenant;
    }

    private function findRouteByIdOrThrowException(int $id)
    {
        if (!($route = $this->routeRepository->findOneBy(['id' => $id])) instanceof RouteInterface) {
            throw UnexpectedTypeException::unexpectedType(
                is_object($route) ? get_class($route) : gettype($route),
                RouteInterface::class);
        }

        return $route;
    }
}
