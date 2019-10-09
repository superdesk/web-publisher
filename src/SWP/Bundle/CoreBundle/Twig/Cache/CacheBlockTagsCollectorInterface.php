<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig\Cache;

interface CacheBlockTagsCollectorInterface
{
    public function startNewCacheBlock(string $key): void;

    public function addTagToCurrentCacheBlock(string $tag): void;

    public function getTags(string $key): array;

    public function flushCurrentCacheBlockTags(): void;
}
