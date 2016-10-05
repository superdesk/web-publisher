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

use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * LoaderInterface is the interface all loaders must implement.
 */
interface LoaderInterface
{
    const SINGLE = 0;
    const COLLECTION = 1;

    /**
     * Loads a Meta class from given datasource.
     *
     * @param string     $metaType     object type
     * @param array|null $parameters   parameters needed to load required object type
     * @param int        $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
     *
     * @return bool|Meta|MetaCollection false if meta cannot be loaded, a Meta instance otherwise or MetaCollection instance (in case of LoaderInterface::COLLECTION response type)
     */
    public function load($metaType, $parameters = [], $responseType = self::SINGLE);

    /**
     * Check if loader support required type.
     *
     * @param string $type required type
     *
     * @return bool false if loader don't support this type, true otherwise
     */
    public function isSupported(string $type) : bool;
}
