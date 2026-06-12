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

namespace SWP\Component\TwigCacheExtension;

interface CacheProviderInterface
{
    /**
     * @return mixed false when no block is cached
     */
    public function fetch(string $key);

    public function save(string $key, string $value, int $lifetime = 0): bool;
}
