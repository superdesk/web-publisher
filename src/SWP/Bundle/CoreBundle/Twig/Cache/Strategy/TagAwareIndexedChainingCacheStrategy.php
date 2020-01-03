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

namespace SWP\Bundle\CoreBundle\Twig\Cache\Strategy;

use Asm89\Twig\CacheExtension\CacheStrategy\IndexedChainingCacheStrategy as BaseIndexedChainingCacheStrategy;
use FOS\HttpCache\ResponseTagger;
use SWP\Bundle\ContentBundle\Twig\Cache\CacheBlockTagsCollectorInterface;

class TagAwareIndexedChainingCacheStrategy extends BaseIndexedChainingCacheStrategy
{
    private $tagsCollector;

    private $responseTagger;

    public function __construct(
        CacheBlockTagsCollectorInterface $tagsCollector,
        ResponseTagger $responseTagger,
        array $strategies
    ) {
        $this->tagsCollector = $tagsCollector;
        $this->responseTagger = $responseTagger;

        parent::__construct($strategies);
    }

    public function fetchBlock($key)
    {
        $fetchedBlock = parent::fetchBlock($key);
        if (false === $fetchedBlock) {
            $this->tagsCollector->startNewCacheBlock($this->getKeyString($key));
        } else {
            $this->responseTagger->addTags($this->tagsCollector->getSavedCacheBlockTags($this->getKeyString($key)));
        }

        return $fetchedBlock;
    }

    public function saveBlock($key, $block)
    {
        $this->tagsCollector->flushCurrentCacheBlockTags();
        $this->responseTagger->addTags($this->tagsCollector->getCurrentCacheBlockTags());

        return parent::saveBlock($key, $block);
    }

    private function getKeyString(array $key): string
    {
        return md5(json_encode($key, JSON_THROW_ON_ERROR, 512));
    }
}
