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
use PHPCR\Util\NodeHelper;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleMedia;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Image;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ImageRendition;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
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

    public function onArticleCreate(ArticleEvent $event)
    {
        $package = $event->getPackage();
        $article = $event->getArticle();
        $this->objectManager->persist($article);

        if (0 === count($package->getItems())) {
            return;
        }

        foreach ($package->getItems() as $key => $packageItem) {
            // create document node for media
            $mediaDocument = $this->createGenericDocument('media', $article);
            if ($packageItem->getType() === 'picture' || $packageItem->getType() === 'file') {
                $this->handleMedia($article, $mediaDocument, $key, $packageItem);
            }

            if (0 !== count($packageItem->getItems())) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if ($item->getType() === 'picture' || $item->getType() === 'file') {
                        $this->handleMedia($article, $mediaDocument, $key, $item);
                    }
                }
            }
        }
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

    private function handleMedia($article, $mediaDocument, $key, $item)
    {
        $articleMedia = new ArticleMedia();
        $articleMedia->setId($key);
        $articleMedia->setParent($mediaDocument);
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);

        if ($item->getType() === 'picture') {
            if (0 === count($item->getRenditions())) {
                return;
            }

            /* @var $rendition Rendition */
            $originalRendition = $item->getRenditions()['original'];
            $articleMedia->setMimetype($originalRendition->getMimetype());
            $image = $this->objectManager->find(Image::class, $this->pathBuilder->build($this->mediaBasepath).'/'.$originalRendition->getMedia());
            $articleMedia->setImage($image);

            // create document node for renditions
            $renditionsDocument = $this->createGenericDocument('renditions', $articleMedia);
            foreach ($item->getRenditions() as $key => $rendition) {
                /* @var $rendition Rendition */
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
        } elseif ($item->getType() === 'file') {
            //TODO: handle files upload
        }
        $this->objectManager->persist($articleMedia);
    }
}
