<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Loader;

use function json_encode;

class MemoryCachedLoader implements LoaderInterface
{
    private $decoratedLoader;

    private $loadedData = [];

    public function __construct(LoaderInterface $decoratedLoader)
    {
        $this->decoratedLoader = $decoratedLoader;
    }

    public function load($metaType, $withParameters = [], $withoutParameters = [], $responseType = self::SINGLE)
    {
        $cacheKey = $this->getCacheKey($metaType, $withParameters, $withoutParameters, $responseType);
        if (array_key_exists($cacheKey, $this->loadedData)) {
            return $this->loadedData[$cacheKey];
        }

        $loadedData = $this->decoratedLoader->load($metaType, $withParameters, $withoutParameters, $responseType);
        $this->loadedData[$cacheKey] = $loadedData;

        return $loadedData;
    }

    public function isSupported(string $type): bool
    {
        return $this->decoratedLoader->isSupported($type);
    }

    private function getCacheKey(string $metaType, array $withParameters, array $withoutParameters, int $responseType): string
    {
        $keys = json_encode([$metaType, $withParameters, $withoutParameters, $responseType], JSON_THROW_ON_ERROR, 512);

        return md5($keys);
    }
}
