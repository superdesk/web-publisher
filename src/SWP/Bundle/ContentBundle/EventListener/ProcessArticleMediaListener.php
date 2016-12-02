<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use Symfony\Component\DomCrawler\Crawler;

class ProcessArticleMediaListener
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * @var MediaFactoryInterface
     */
    protected $mediaFactory;

    /**
     * ProcessArticleMediaListener constructor.
     *
     * @param ObjectManager         $objectManager
     * @param MediaManagerInterface $mediaManager
     * @param MediaFactoryInterface $mediaFactory
     */
    public function __construct(
        ObjectManager $objectManager,
        MediaManagerInterface $mediaManager,
        MediaFactoryInterface $mediaFactory
    ) {
        $this->objectManager = $objectManager;
        $this->mediaManager = $mediaManager;
        $this->mediaFactory = $mediaFactory;
    }

    /**
     * @param ArticleEvent $event
     */
    public function onArticleCreate(ArticleEvent $event)
    {
        $package = $event->getPackage();
        $article = $event->getArticle();
        $this->objectManager->persist($article);

        if (null !== $package && 0 === count($package->getItems())) {
            return;
        }

        foreach ($package->getItems() as $key => $packageItem) {
            if (ItemInterface::TYPE_PICTURE === $packageItem->getType() || ItemInterface::TYPE_FILE === $packageItem->getType()) {
                $articleMedia = $this->handleMedia($article, $key, $packageItem);
                $this->objectManager->persist($articleMedia);
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                        $articleMedia = $this->handleMedia($article, $key, $item);
                        $this->objectManager->persist($articleMedia);
                    }
                }
            }
        }
    }

    /**
     * @param ArticleInterface $article
     * @param string           $key
     * @param ItemInterface    $item
     *
     * @return ArticleMediaInterface
     */
    public function handleMedia(ArticleInterface $article, string $key, ItemInterface $item)
    {
        $articleMedia = $this->mediaFactory->create($article, $key, $item);

        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            $this->replaceBodyImagesWithMedia($article, $articleMedia);
        } elseif (ItemInterface::TYPE_FILE === $item->getType()) {
            //TODO: handle files upload
        }

        return $articleMedia;
    }

    /**
     * @param ArticleInterface      $article
     * @param ArticleMediaInterface $articleMedia
     */
    private function replaceBodyImagesWithMedia(ArticleInterface $article, ArticleMediaInterface $articleMedia)
    {
        $body = $article->getBody();
        $mediaId = $articleMedia->getKey();
        preg_match(
            "/(<!-- EMBED START Image {id: \"$mediaId\"} -->)(.+?)(<!-- EMBED END Image {id: \"$mediaId\"} -->)/im",
            str_replace(PHP_EOL, '', $body),
            $embeds
        );
        if (empty($embeds)) {
            return;
        }

        $figureString = $embeds[2];
        $crawler = new Crawler($figureString);
        $images = $crawler->filter('figure img');
        /** @var \DOMElement $imageElement */
        foreach ($images as $imageElement) {
            foreach ($articleMedia->getRenditions() as $rendition) {
                if (strpos($imageElement->getAttribute('src'), $rendition->getImage()->getAssetId()) !== false) {
                    $attributes = $imageElement->attributes;
                    while ($attributes->length) {
                        $imageElement->removeAttribute($attributes->item(0)->name);
                    }
                    $imageElement->setAttribute('src', $this->mediaManager->getMediaUri($rendition->getImage()));
                    $imageElement->setAttribute('data-media-id', $mediaId);
                    $imageElement->setAttribute('data-image-id', $rendition->getImage()->getAssetId());
                }
            }
        }

        $article->setBody(str_replace($figureString, $crawler->filter('body')->html(), $body));
    }
}
