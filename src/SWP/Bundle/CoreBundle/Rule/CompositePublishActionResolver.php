<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Rule;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Factory\PublishDestinationFactoryInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class CompositePublishActionResolver implements CompositePublishActionResolverInterface
{
    private $tenantRepository;
    private $routeRepository;
    private $publishDestinationFactory;

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
