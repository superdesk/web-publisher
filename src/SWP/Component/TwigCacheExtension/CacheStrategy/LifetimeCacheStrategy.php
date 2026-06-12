<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Twig Cache Extension.
 *
 * Twig 3 port of the abandoned asm89 twig/cache-extension (MIT).
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 */

namespace SWP\Component\TwigCacheExtension\CacheStrategy;

use SWP\Component\TwigCacheExtension\CacheProviderInterface;
use SWP\Component\TwigCacheExtension\CacheStrategyInterface;
use SWP\Component\TwigCacheExtension\Exception\InvalidCacheLifetimeException;

class LifetimeCacheStrategy implements CacheStrategyInterface
{
    protected CacheProviderInterface $cache;

    public function __construct(CacheProviderInterface $cache)
    {
        $this->cache = $cache;
    }

    public function fetchBlock($key)
    {
        return $this->cache->fetch($key['key']);
    }

    public function generateKey($annotation, $value)
    {
        if (!is_numeric($value)) {
            throw new InvalidCacheLifetimeException($value);
        }

        return [
            'lifetime' => $value,
            'key' => '__LCS__'.$annotation,
        ];
    }

    public function saveBlock($key, $block)
    {
        return $this->cache->save($key['key'], $block, (int) $key['lifetime']);
    }
}
