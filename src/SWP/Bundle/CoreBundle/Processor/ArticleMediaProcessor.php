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
use SWP\Component\Storage\Factory\FactoryInterface;

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

    /**
     * @var FactoryInterface
     */
    private $slideshowFactory;

    /**
     * @var FactoryInterface
     */
    private $slideshowItemFactory;

    public function __construct(
        MediaFactoryInterface $mediaFactory,
        ArticleBodyProcessorInterface $articleBodyProcessor,
        FactoryInterface $slideshowFactory,
        FactoryInterface $slideshowItemFactory
    ) {
        $this->mediaFactory = $mediaFactory;
        $this->articleBodyProcessor = $articleBodyProcessor;
        $this->slideshowFactory = $slideshowFactory;
        $this->slideshowItemFactory = $slideshowItemFactory;
    }

    public function fillArticleMedia(PackageInterface $package, ArticleInterface $article): void
    {
        $this->handleSlideshows($package, $article);

        if (0 === \count($package->getItems())) {
            return;
        }

        $articleMedia = new ArrayCollection();
        foreach ($package->getItems() as $packageItem) {
            if (in_array($packageItem->getType(), [ItemInterface::TYPE_TEXT, ItemInterface::TYPE_COMPOSITE], true)) {
                continue;
            }

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

    private function handleSlideshows(PackageInterface $package, ArticleInterface $article): void
    {
        foreach ($package->getGroups()->toArray() as $packageSlideshow) {
            $slideshow = $this->slideshowFactory->create();
            $slideshow->setCode($packageSlideshow->getCode());

            foreach ($packageSlideshow->getItems()->toArray() as $packageSlideshowItem) {
                $slideshowItem = $this->slideshowItemFactory->create();
                $articleMedia = $this->mediaFactory->create($article, $packageSlideshowItem->getName(), $packageSlideshowItem);
                $slideshowItem->setArticleMedia($articleMedia);
                $slideshowItem->setSlideshow($slideshow);

                $slideshow->addSlideshowItem($slideshowItem);
            }

            $article->addSlideshow($slideshow);
        }
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
