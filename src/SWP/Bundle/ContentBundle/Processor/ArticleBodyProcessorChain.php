<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Processor;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use Zend\Stdlib\PriorityQueue;

final class ArticleBodyProcessorChain implements ArticleBodyProcessorInterface
{
    /**
     * @var PriorityQueue
     */
    private $processors;

    public function __construct()
    {
        $this->processors = new PriorityQueue();
    }

    public function process(ArticleInterface $article, ArticleMediaInterface $articleMedia): void
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($articleMedia->getMimetype())) {
                dump(get_class($processor));
                $processor->process($article, $articleMedia);
            }
        }
    }

    public function supports(string $type): bool
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($type)) {
                return true;
            }
        }

        return false;
    }

    public function addProcessor(ArticleBodyProcessorInterface $articleBodyProcessor, int $priority = 0): void
    {
        $this->processors->insert($articleBodyProcessor, $priority);
    }
}
