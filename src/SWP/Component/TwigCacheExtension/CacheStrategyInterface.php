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

interface CacheStrategyInterface
{
    /**
     * Fetch the block for the given key.
     *
     * @param mixed $key
     *
     * @return mixed false when no block is cached, the block otherwise
     */
    public function fetchBlock($key);

    /**
     * Generate a key for the given annotation and value.
     *
     * @param string $annotation
     * @param mixed  $value
     *
     * @return mixed
     */
    public function generateKey($annotation, $value);

    /**
     * Save the rendered block.
     *
     * @param mixed  $key
     * @param string $block
     *
     * @return mixed
     */
    public function saveBlock($key, $block);
}
