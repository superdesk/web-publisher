<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
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
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\Rendition;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

class ProcessArticleMediaListener
{
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
            if ($packageItem->getType() === 'picture' || $packageItem->getType() === 'file') {
                $articleMedia = $this->handleMedia($article, $mediaDocument, $key, $packageItem);
                $this->objectManager->persist($articleMedia);
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if (ItemInterface::TYPE_PICTURE === $item->getType() || ItemInterface::TYPE_TEXT === $item->getType()) {
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

        if (ItemInterface::TYPE_PICTURE  === $item->getType()) {
            $this->createImageMedia($articleMedia, $item);
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
    public function createImageMedia($articleMedia, $item)
    {
        if (0 === $item->getRenditions()->count()) {
            return;
        }

        /* @var $originalRendition Rendition */
        $originalRendition = $item->getRenditions()['original'];
        $articleMedia->setMimetype($originalRendition->getMimetype());
        $image = $this->objectManager->find(Image::class, $this->pathBuilder->build($this->mediaBasepath).'/'.$originalRendition->getMedia());
        $articleMedia->setImage($image);

        // create document node for renditions
        $renditionsDocument = $this->createGenericDocument('renditions', $articleMedia);
        /* @var $rendition Rendition */
        foreach ($item->getRenditions() as $key => $rendition) {
            $image = $this->objectManager->find(Image::class, $this->pathBuilder->build($this->mediaBasepath).'/'.$rendition->getMedia());
            if (null === $image) {
                return;
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

    private function createGenericDocument($nodeName, $parent)
    {
        $document = new Generic();
        $document
            ->setParentDocument($parent)
            ->setNodename($nodeName);

        $this->objectManager->persist($document);

        return $document;
    }
}
