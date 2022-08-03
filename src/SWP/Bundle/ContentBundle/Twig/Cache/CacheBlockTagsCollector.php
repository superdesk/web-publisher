<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Twig\Cache;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\Cache\CacheInterface;

class CacheBlockTagsCollector implements CacheBlockTagsCollectorInterface
{
    private $currentCacheBlockKey = null;

    private $currentCacheBlockTags;

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->currentCacheBlockTags = new ArrayCollection();
        $this->cache = $cache;
    }

    public function startNewCacheBlock(string $key): void
    {
        $this->currentCacheBlockKey = $key;
    }

    public function addTagToCurrentCacheBlock(string $tag): void
    {
        if (null !== $this->currentCacheBlockKey && !$this->currentCacheBlockTags->contains($tag)) {
            $this->currentCacheBlockTags->add($tag);
        }
    }

    public function flushCurrentCacheBlockTags(): void
    {
        if (null !== $this->currentCacheBlockKey) {
            $this->getSavedCacheBlockTags($this->currentCacheBlockKey);
        }

        $this->currentCacheBlockKey = null;
    }

    public function getCurrentCacheBlockTags(): array
    {
        return $this->currentCacheBlockTags->toArray();
    }

    public function getSavedCacheBlockTags(string $cacheKey)
    {
        return $this->cache->get($cacheKey, function () {
            if (null !== $this->currentCacheBlockKey) {
                $this->currentCacheBlockKey = null;

                if ($this->currentCacheBlockTags instanceof ArrayCollection) {
                    return $this->currentCacheBlockTags->toArray();
                }
            }

            return [];
        });
    }
}
