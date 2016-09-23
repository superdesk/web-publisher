<?php

/**
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
use Doctrine\ODM\PHPCR\Document\Generic;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleMedia;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Image;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ImageRendition;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Component\DomCrawler\Crawler;

class ProcessArticleMediaListener
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var string
     */
    protected $mediaBasepath;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * ProcessArticleMediaListener constructor.
     *
     * @param ObjectManager                   $objectManager
     * @param TenantAwarePathBuilderInterface $pathBuilder
     * @param string                          $mediaBasepath
     * @param MediaManagerInterface           $mediaManager
     */
    public function __construct(
        ObjectManager $objectManager,
        TenantAwarePathBuilderInterface $pathBuilder,
        $mediaBasepath,
        MediaManagerInterface $mediaManager
    ) {
        $this->objectManager = $objectManager;
        $this->pathBuilder = $pathBuilder;
        $this->mediaBasepath = $mediaBasepath;
        $this->mediaManager = $mediaManager;
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
            // create document node for media
            $mediaDocument = $this->createGenericDocument('media', $article);
            if (ItemInterface::TYPE_PICTURE === $packageItem->getType() || ItemInterface::TYPE_FILE === $packageItem->getType()) {
                $articleMedia = $this->handleMedia($article, $mediaDocument, $key, $packageItem);
                $this->objectManager->persist($articleMedia);
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_FILE === $item->getType()) {
                        $articleMedia = $this->handleMedia($article, $mediaDocument, $key, $item);
                        $this->objectManager->persist($articleMedia);
                    }
                }
            }
        }
    }

    /**
     * @param ArticleInterface $article
     * @param Generic          $mediaDocument
     * @param string           $key
     * @param ItemInterface    $item
     *
     * @return ArticleMedia
     */
    public function handleMedia(ArticleInterface $article, Generic $mediaDocument, $key, ItemInterface $item)
    {
        $articleMedia = new ArticleMedia();
        $articleMedia->setId($key);
        $articleMedia->setParent($mediaDocument);
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);

        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            $this->createImageMedia($articleMedia, $item);
            $this->replaceBodyImagesWithMedia($article, $articleMedia);
        } elseif (ItemInterface::TYPE_FILE === $item->getType()) {
            //TODO: handle files upload
        }

        return $articleMedia;
    }

    /**
     * @param ArticleMedia  $articleMedia
     * @param ItemInterface $item
     *
     * @return ArticleMedia
     */
    public function createImageMedia(ArticleMedia $articleMedia, ItemInterface $item)
    {
        if (0 === $item->getRenditions()->count()) {
            return;
        }

        $originalRendition = $item->getRenditions()['original'];
        $articleMedia->setMimetype($originalRendition->getMimetype());
        $image = $this->objectManager->find(
            Image::class,
            $this->pathBuilder->build($this->mediaBasepath).'/'.$this->mediaManager->handleMediaId($originalRendition->getMedia())
        );
        $articleMedia->setImage($image);

        // create document node for renditions
        $renditionsDocument = $this->createGenericDocument('renditions', $articleMedia);
        foreach ($item->getRenditions() as $key => $rendition) {
            $image = $this->objectManager->find(
                Image::class,
                $this->pathBuilder->build($this->mediaBasepath).'/'.$this->mediaManager->handleMediaId($rendition->getMedia())
            );
            if (null === $image) {
                continue;
            }

            $imageRendition = new ImageRendition();
            $imageRendition->setParent($renditionsDocument);
            $imageRendition->setImage($image);
            $imageRendition->setMedia($articleMedia);
            $imageRendition->setHeight($rendition->getHeight());
            $imageRendition->setWidth($rendition->getWidth());
            $imageRendition->setName($key);
            $this->objectManager->persist($imageRendition);
            $articleMedia->addRendition($imageRendition);
        }

        return $articleMedia;
    }

    /**
     * @param ArticleInterface $article
     * @param ArticleMedia     $articleMedia
     */
    private function replaceBodyImagesWithMedia(ArticleInterface $article, ArticleMedia $articleMedia)
    {
        $body = $article->getBody();
        $mediaId = $articleMedia->getId();

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
                if (strpos($imageElement->getAttribute('src'), $rendition->getImage()->getId()) !== false) {
                    $attributes = $imageElement->attributes;
                    while ($attributes->length) {
                        $imageElement->removeAttribute($attributes->item(0)->name);
                    }
                    $imageElement->setAttribute('src', $this->mediaManager->getMediaUri($rendition->getImage()));
                    $imageElement->setAttribute('data-media-id', $mediaId);
                    $imageElement->setAttribute('data-image-id', $rendition->getImage()->getId());
                }
            }
        }

        $article->setBody(str_replace($figureString, $crawler->filter('body')->html(), $body));
    }

    /**
     * @param string $nodeName
     * @param mixed  $parent
     *
     * @return Generic
     */
    private function createGenericDocument($nodeName, $parent)
    {
        if (null !== $document = $this->objectManager->find(Generic::class, $parent->getId().'/'.$nodeName)) {
            return $document;
        }

        $document = new Generic();
        $document
            ->setParentDocument($parent)
            ->setNodename($nodeName);

        $this->objectManager->persist($document);

        return $document;
    }
}
