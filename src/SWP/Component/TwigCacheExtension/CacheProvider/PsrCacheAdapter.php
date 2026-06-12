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

namespace SWP\Component\TwigCacheExtension\CacheProvider;

use Psr\Cache\CacheItemPoolInterface;
use SWP\Component\TwigCacheExtension\CacheProviderInterface;

class PsrCacheAdapter implements CacheProviderInterface
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function fetch(string $key)
    {
        $item = $this->cache->getItem(self::sanitizeKey($key));

        return $item->isHit() ? $item->get() : false;
    }

    public function save(string $key, string $value, int $lifetime = 0): bool
    {
        $item = $this->cache->getItem(self::sanitizeKey($key));
        $item->set($value);
        if ($lifetime > 0) {
            $item->expiresAfter($lifetime);
        }

        return $this->cache->save($item);
    }

    private static function sanitizeKey(string $key): string
    {
        return preg_replace('#[{}()/\\\\@:]#', '_', $key);
    }
}
