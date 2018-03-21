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
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Processor\ArticleBodyProcessor as BaseProcessor;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Bridge\Model\ItemInterface;

final class ArticleBodyProcessor extends BaseProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var MediaFactoryInterface
     */
    private $mediaFactory;

    /**
     * ArticleBodyProcessor constructor.
     *
     * @param MediaManagerInterface $mediaManager
     * @param MediaFactoryInterface $mediaFactory
     */
    public function __construct(MediaManagerInterface $mediaManager, MediaFactoryInterface $mediaFactory)
    {
        parent::__construct($mediaManager);

        $this->mediaFactory = $mediaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function fillArticleMedia(PackageInterface $package, ArticleInterface $article): void
    {
        if (null === $package || (null !== $package && 0 === count($package->getItems()))) {
            return;
        }

        $articleMedia = new ArrayCollection();
        foreach ($package->getItems() as $packageItem) {
            $key = $packageItem->getName();
            if (ItemInterface::TYPE_PICTURE === $packageItem->getType() || ItemInterface::TYPE_FILE === $packageItem->getType()) {
                $articleMedia->add($this->handleMedia($article, $key, $packageItem));
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                        $articleMedia->add($this->handleMedia($article, $key, $item));
                    }
                }
            }
        }

        $article->setMedia($articleMedia);
    }

    /**
     * @param ArticleInterface $article
     * @param string           $key
     * @param ItemInterface    $item
     *
     * @return ArticleMediaInterface
     */
    private function handleMedia(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia = $this->mediaFactory->create($article, $key, $item);
        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            $this->replaceBodyImagesWithMedia($article, $articleMedia);
        }

        if (ArticleInterface::KEY_FEATURE_MEDIA === $key) {
            $article->setFeatureMedia($articleMedia);
        }

        return $articleMedia;
    }
}
