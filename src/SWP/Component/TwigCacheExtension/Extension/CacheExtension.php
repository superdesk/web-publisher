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

namespace SWP\Component\TwigCacheExtension\Extension;

use SWP\Component\TwigCacheExtension\CacheStrategyInterface;
use SWP\Component\TwigCacheExtension\TokenParser\CacheTokenParser;
use Twig\Extension\AbstractExtension;

class CacheExtension extends AbstractExtension
{
    private CacheStrategyInterface $cacheStrategy;

    public function __construct(CacheStrategyInterface $cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
    }

    public function getCacheStrategy(): CacheStrategyInterface
    {
        return $this->cacheStrategy;
    }

    public function getTokenParsers(): array
    {
        return [
            new CacheTokenParser(),
        ];
    }
}
