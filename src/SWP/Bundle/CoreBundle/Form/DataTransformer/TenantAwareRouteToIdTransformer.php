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

namespace SWP\Bundle\CoreBundle\Form\DataTransformer;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class TenantAwareRouteToIdTransformer implements DataTransformerInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    /**
     * TenantAwareRouteToIdTransformer constructor.
     *
     * @param TenantContextInterface   $tenantContext
     * @param RouteRepositoryInterface $routeRepository
     */
    public function __construct(TenantContextInterface $tenantContext, RouteRepositoryInterface $routeRepository)
    {
        $this->tenantContext = $tenantContext;
        $this->routeRepository = $routeRepository;
    }

    /**
     * Transforms an object (route) to a string (id).
     *
     * @param RouteInterface|string $route
     *
     * @return string
     *
     * @throws TransformationFailedException if object (route) is of wrong type
     */
    public function transform($route)
    {
        if (null === $route) {
            return;
        }

        if (!$route instanceof RouteInterface) {
            throw new UnexpectedTypeException($route, RouteInterface::class);
        }

        return $route->getId();
    }

    /**
     * Transforms an id to an object (route).
     *
     * @param string $routeId
     *
     * @return RouteInterface
     *
     * @throws TransformationFailedException if object (route) is not found
     */
    public function reverseTransform($routeId)
    {
        if (null === $routeId) {
            return;
        }

        $tenantCode = $this->tenantContext->getTenant()->getCode();

        /** @var RouteInterface $route */
        $route = $this->routeRepository->findOneBy([
            'id' => $routeId,
            'tenantCode' => $tenantCode,
        ]);

        if (null === $route) {
            throw new TransformationFailedException(sprintf(
                'Route with id "%s" does not exist for "%s" tenant!',
                $routeId,
                $tenantCode
            ));
        }

        return $route;
    }
}
