<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Routing;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MetaRouter extends DynamicRouter
{
    protected $internalRoutesCache = [];

    /**
     * @param string|Meta     $name
     * @param array           $parameters
     * @param bool|int|string $referenceType
     *
     * @return mixed|string
     *
     * @throws RouteNotFoundException
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $cacheKey = $this->getCacheKey($name, $parameters, $referenceType);
        if (array_key_exists($cacheKey, $this->internalRoutesCache)) {
            return $this->internalRoutesCache[$cacheKey];
        }

        if ($name instanceof Meta && $name->getValues() instanceof ArticleInterface) {
            $parameters['slug'] = $name->getValues()->getSlug();
            $route = $name->getValues()->getRoute();

            if (null === $route && $name->getContext()->getCurrentPage()) {
                $parameters['slug'] = null;
                $route = $name->getContext()->getCurrentPage()->getValues();
            }
        } elseif ($name instanceof Meta && $name->getValues() instanceof RouteInterface) {
            $route = $name->getValues();
        } else {
            $route = $name;
        }

        if (null === $route || is_array($route)) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        $result = parent::generate($route, $parameters, $referenceType);
        $this->internalRoutesCache[$cacheKey] = $result;
        unset($route, $name, $parameters);

        return $result;
    }

    private function getCacheKey($route, $parameters, $type)
    {
        if ($route instanceof Meta && $route->getValues() instanceof ArticleInterface) {
            $name = $route->getValues()->getId();
        } elseif ($route instanceof Meta && $route->getValues() instanceof RouteInterface) {
            $name = $route->getValues()->getName();
        } elseif ($route instanceof RouteInterface) {
            $name = $route->getName();
        } else {
            $name = $route;
        }

        return md5($name.serialize($parameters).$type);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return ($name instanceof Meta && (
            $name->getValues() instanceof ArticleInterface ||
            $name->getValues() instanceof RouteInterface
        )) || $name instanceof RouteInterface || (is_string($name) && 'homepage' !== $name);
    }
}
