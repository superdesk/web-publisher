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

use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

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
     *  {@inheritdoc}
     */
    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
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
    public function isSupported(string $type): bool
    {
        return self::SUPPORTED_TYPE === $type;
    }
}
