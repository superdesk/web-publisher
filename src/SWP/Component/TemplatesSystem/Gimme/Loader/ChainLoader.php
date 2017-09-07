<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Loader;

/**
 * ChainLoader is a loader that calls other loaders to load Meta objects.
 */
class ChainLoader implements LoaderInterface
{
    protected $loaders = [];

    /**
     * Adds a loader instance.
     *
     * @param LoaderInterface $loader A Loader instance
     */
    public function addLoader(LoaderInterface $loader)
    {
        if (false !== $key = array_search($loader, $this->loaders)) {
            $this->loaders[$key] = $loader;
        } else {
            $this->loaders[] = $loader;
        }
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return bool
     */
    public function removeLoader(LoaderInterface $loader)
    {
        if (false !== $key = array_search($loader, $this->loaders)) {
            unset($this->loaders[$key]);

            return true;
        }

        return false;
    }

    /**
     *  {@inheritdoc}
     */
    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->isSupported($type)) {
                if (false !== $meta = $loader->load($type, $parameters, $withoutParameters, $responseType)) {
                    return $meta;
                }
            }
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
        foreach ($this->loaders as $loader) {
            if ($loader->isSupported($type)) {
                return true;
            }
        }

        return false;
    }
}
