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

use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Class RouteLoader.
 */
class RouteLoader implements LoaderInterface
{
    const SUPPORTED_TYPE = 'route';

    /**
     * @var MetaFactoryInterface
     */
    protected $metaFactory;

    /**
     * RouteLoader constructor.
     *
     * @param MetaFactoryInterface $metaFactory
     */
    public function __construct(MetaFactoryInterface $metaFactory)
    {
        $this->metaFactory = $metaFactory;
    }

    /**
     * Load meta object by provided type and parameters.
     *
     * @MetaLoaderDoc(
     *     description="Article Loader loads articles from Content Repository",
     *     parameters={
     *         route_object="SINGLE|required route object"
     *     }
     * )
     *
     * @param string $type         object type
     * @param array  $parameters   parameters needed to load required object type
     * @param int    $responseType response type: single meta (LoaderInterface::SINGLE)
     *
     * @return Meta|bool false if meta cannot be loaded, a Meta instance otherwise
     */
    public function load($type, $parameters = [], $responseType = LoaderInterface::SINGLE)
    {
        $route = isset($parameters['route_object']) ? $parameters['route_object'] : null;

        if (null !== $route) {
            return $this->metaFactory->create($route);
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
    public function isSupported($type)
    {
        return self::SUPPORTED_TYPE === $type;
    }
}
