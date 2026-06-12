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

class GenerationalCacheStrategy implements CacheStrategyInterface
{
    private CacheProviderInterface $cache;

    private KeyGeneratorInterface $keyGenerator;

    private int $lifetime;

    public function __construct(CacheProviderInterface $cache, KeyGeneratorInterface $keyGenerator, int $lifetime = 0)
    {
        $this->cache = $cache;
        $this->keyGenerator = $keyGenerator;
        $this->lifetime = $lifetime;
    }

    public function fetchBlock($key)
    {
        return $this->cache->fetch($key);
    }

    public function generateKey($annotation, $value)
    {
        $key = $this->keyGenerator->generateKey($value);

        if (null === $key) {
            throw new \RuntimeException('Key generator did not generate a key.');
        }

        return $annotation.'__GCS__'.$key;
    }

    public function saveBlock($key, $block)
    {
        return $this->cache->save($key, $block, $this->lifetime);
    }
}
