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

use SWP\Component\TwigCacheExtension\CacheStrategyInterface;

class IndexedChainingCacheStrategy implements CacheStrategyInterface
{
    /**
     * @var CacheStrategyInterface[]
     */
    private array $strategies;

    /**
     * @param CacheStrategyInterface[] $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function fetchBlock($key)
    {
        return $this->getStrategy($key['strategyKey'])->fetchBlock($key['key']);
    }

    public function generateKey($annotation, $value)
    {
        if (!\is_array($value)) {
            throw new \RuntimeException(sprintf('Value should be an array of "[strategyKey => value]", got "%s".', get_debug_type($value)));
        }

        $strategyKey = key($value);
        if (null === $strategyKey) {
            throw new \RuntimeException('No strategy key found in value.');
        }

        $value = current($value);

        return [
            'strategyKey' => $strategyKey,
            'key' => $this->getStrategy($strategyKey)->generateKey($annotation, $value),
        ];
    }

    public function saveBlock($key, $block)
    {
        return $this->getStrategy($key['strategyKey'])->saveBlock($key['key'], $block);
    }

    private function getStrategy(string $strategyKey): CacheStrategyInterface
    {
        if (!isset($this->strategies[$strategyKey])) {
            throw new \RuntimeException(sprintf('No strategy configured with key "%s".', $strategyKey));
        }

        return $this->strategies[$strategyKey];
    }
}
