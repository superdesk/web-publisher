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
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MetaRouter extends DynamicRouter
{
    protected $internalRoutesCache = [];

    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $cacheKey = $this->getCacheKey($name, $parameters, $referenceType);
        if (array_key_exists($cacheKey, $this->internalRoutesCache)) {
            return $this->internalRoutesCache[$cacheKey];
        }

        $route = $name;
        if ($name instanceof Meta) {
            if ($name->getValues() instanceof ArticleInterface) {
                $parameters['slug'] = $name->getValues()->getSlug();
                $route = $name->getValues()->getRoute();

                if (null === $route && $name->getContext()->getCurrentPage()) {
                    $parameters['slug'] = null;
                    $route = $name->getContext()->getCurrentPage()->getValues();
                }
            } elseif ($name->getValues() instanceof RouteInterface) {
                $route = $name->getValues();
            }
        } elseif ($name instanceof ArticleInterface) {
            $route = $name->getRoute();
            $parameters['slug'] = $name->getSlug();
        }

        if (null === $route || is_array($route)) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        $result = parent::generate($route, $parameters, $referenceType);
        $this->internalRoutesCache[$cacheKey] = $result;
        unset($route);

        return $result;
    }

    private function getCacheKey($route, $parameters, $type)
    {
        $name = $route;
        if ($route instanceof Meta) {
            if ($route->getValues() instanceof ArticleInterface) {
                $name = $route->getValues()->getId();
            } elseif ($route->getValues() instanceof RouteInterface) {
                $name = $route->getValues()->getName();
            }
        } elseif ($route instanceof RouteInterface) {
            $name = $route->getName();
        } elseif ($route instanceof ArticleInterface) {
            $name = $route->getId();
        }

        return md5($name.serialize($parameters).$type);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return
        ($name instanceof Meta && (
            $name->getValues() instanceof ArticleInterface ||
            $name->getValues() instanceof RouteInterface
        )) ||
        $name instanceof RouteInterface ||
        $name instanceof ArticleInterface ||
        (
            is_string($name) &&
            'homepage' !== $name &&
            'swp_author_media_get' !== $name &&
            'swp_media_get' !== $name &&
            false === strpos($name, 'swp_api_')
        );
    }
}
