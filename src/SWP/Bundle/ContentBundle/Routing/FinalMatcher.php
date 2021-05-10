<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Routing;

use Symfony\Cmf\Component\Routing\NestedMatcher\FinalMatcherInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class FinalMatcher extends RedirectableUrlMatcher implements FinalMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function finalMatch(RouteCollection $collection, Request $request)
    {
        $this->routes = $collection;
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->setContext($context);

        return $this->match($request->getPathInfo());
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributes(Route $route, $name, array $attributes)
    {
        if ($route instanceof RouteObjectInterface && is_string($route->getRouteKey())) {
            $name = $route->getRouteKey();
        }
        $attributes[RouteObjectInterface::ROUTE_NAME] = $name;
        $attributes[RouteObjectInterface::ROUTE_OBJECT] = $route;

        return $this->mergeDefaults($attributes, $route->getDefaults());
    }
}
