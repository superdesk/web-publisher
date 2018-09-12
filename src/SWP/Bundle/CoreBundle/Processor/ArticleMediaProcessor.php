<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessorInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Bridge\Model\ItemInterface;

final class ArticleMediaProcessor implements ArticleMediaProcessorInterface
{
    /**
     * @var MediaFactoryInterface
     */
    private $mediaFactory;

    /**
     * @var ArticleBodyProcessorInterface
     */
    private $articleBodyProcessor;

    public function __construct(
        MediaFactoryInterface $mediaFactory,
        ArticleBodyProcessorInterface $articleBodyProcessor
    ) {
        $this->mediaFactory = $mediaFactory;
        $this->articleBodyProcessor = $articleBodyProcessor;
    }

    public function fillArticleMedia(PackageInterface $package, ArticleInterface $article): void
    {
        if (null === $package || (null !== $package && 0 === \count($package->getItems()))) {
            return;
        }

        $articleMedia = new ArrayCollection();
        foreach ($package->getItems() as $packageItem) {
            $key = $packageItem->getName();
            $articleMedia->add($this->handleMedia($article, $key, $packageItem));

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    $articleMedia->add($this->handleMedia($article, $key, $item));
                }
            }
        }

        $article->setMedia($articleMedia);
    }

    private function handleMedia(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia = $this->mediaFactory->create($article, $key, $item);

        $this->articleBodyProcessor->process($article, $articleMedia);

        if (ArticleInterface::KEY_FEATURE_MEDIA === $key) {
            $article->setFeatureMedia($articleMedia);
        }

        return $articleMedia;
    }
}
