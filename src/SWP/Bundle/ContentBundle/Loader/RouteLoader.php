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
namespace SWP\Bundle\ContentBundle\Loader;

use Doctrine\Common\Cache\CacheProvider;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class RouteLoader extends MetaLoader
{
    const SUPPORTED_TYPE = 'route';

    public function __construct(
        $configurationPath,
        CacheProvider $metadataCache)
    {
        parent::__construct($configurationPath.'/Resources/meta/route.yml', $metadataCache);
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Loader loads articles from Content Repository",
     *     parameters={
     *         contentPath="SINGLE|required content path",
     *         slug="SINGLE|required content slug",
     *         pageName="COLLECTiON|name of Page for required articles"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return Meta|Meta[]|bool false if meta cannot be loaded, a Meta instance otherwise
     */
    public function load($type, $parameters, $responseType = LoaderInterface::SINGLE)
    {
        $route = isset($parameters['route_object']) ? $parameters['route_object'] : null;

        return $this->getRouteMeta($route);
    }

    /**
     * Checks if Loader supports provided type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isSupported($type)
    {
        return self::SUPPORTED_TYPE === $type;
    }

    private function getRouteMeta($route)
    {
        if (null !== $route) {
            return new Meta($this->getConfiguration(), $route);
        }

        return false;
    }
}
