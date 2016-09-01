<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Form\DataTransformer;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class RouteToIdTransformer implements DataTransformerInterface
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * RouteToIdTransformer constructor.
     *
     * @param RouteProviderInterface $routeProvider
     */
    public function __construct(RouteProviderInterface $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }

    /**
     * Transforms an object (route) to a string (id).
     *
     * @param RouteInterface|string $route
     *
     * @return string
     */
    public function transform($route)
    {
        if (null === $route) {
            return '';
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
     * @return RouteInterface|void
     *
     * @throws TransformationFailedException if object (route) is not found.
     */
    public function reverseTransform($routeId)
    {
        if (null === $routeId) {
            return;
        }

        $route = $this->routeProvider->getOneById($routeId);

        if (null === $route) {
            throw new TransformationFailedException(sprintf(
                'Route with id "%s" does not exist!',
                $routeId
            ));
        }

        return $route;
    }
}
