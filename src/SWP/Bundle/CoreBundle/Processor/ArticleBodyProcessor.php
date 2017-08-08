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
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessor as BaseProcessor;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Bridge\Model\ItemInterface;

class ArticleBodyProcessor extends BaseProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @param MediaFactoryInterface $mediaFactory
     * @param PackageInterface      $package
     * @param ArticleInterface      $article
     */
    public function fillArticleMedia(MediaFactoryInterface $mediaFactory, PackageInterface $package, ArticleInterface $article)
    {
        if (null === $package || (null !== $package && 0 === count($package->getItems()))) {
            return;
        }

        $articleMedia = new ArrayCollection();
        foreach ($package->getItems() as $packageItem) {
            $key = $packageItem->getName();
            if (ItemInterface::TYPE_PICTURE === $packageItem->getType() || ItemInterface::TYPE_FILE === $packageItem->getType()) {
                $articleMedia->add($this->handleMedia($mediaFactory, $article, $key, $packageItem));
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                        $articleMedia->add($this->handleMedia($mediaFactory, $article, $key, $item));
                    }
                }
            }
        }

        $article->setMedia($articleMedia);
    }

    /**
     * @param MediaFactoryInterface $mediaFactory
     * @param ArticleInterface      $article
     * @param string                $key
     * @param ItemInterface         $item
     *
     * @return \SWP\Bundle\ContentBundle\Model\ArticleMediaInterface
     */
    private function handleMedia(MediaFactoryInterface $mediaFactory, ArticleInterface $article, string $key, ItemInterface $item)
    {
        $articleMedia = $mediaFactory->create($article, $key, $item);
        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            $this->replaceBodyImagesWithMedia($article, $articleMedia);
        }

        if (ArticleInterface::KEY_FEATURE_MEDIA === $key) {
            $article->setFeatureMedia($articleMedia);
        }

        return $articleMedia;
    }
}
