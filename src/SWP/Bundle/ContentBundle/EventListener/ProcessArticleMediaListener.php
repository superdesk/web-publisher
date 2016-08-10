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

        foreach ($package->getItems() as $packageItem) {
            if (0 === count($packageItem->getItems())) {
                continue;
            }

            // create document node for media
            $mediaDocument = $this->createGenericDocument('media', $article);
            foreach ($packageItem->getItems() as $key => $item) {
                if ($item->getType() === 'picture' || $item->getType() === 'file') {
                    $articleMedia = new ArticleMedia();
                    $articleMedia->setId($key);
                    $articleMedia->setParent($mediaDocument);
                    $articleMedia->setArticle($article);
                    $articleMedia->setFromItem($item);

                    if ($item->getType() === 'picture') {
                        /* @var $rendition Rendition */
                        $originalRendition = $item->getRenditions()['original'];
                        $articleMedia->setMimetype($originalRendition->getMimetype());
                        $image = $this->objectManager->find(Image::class, $this->pathBuilder->build($this->mediaBasepath).'/'.$originalRendition->getMedia());
                        $articleMedia->setImage($image);

                        if (0 === count($item->getRenditions())) {
                            continue;
                        }

                        // create document node for renditions
                        $renditionsDocument = $this->createGenericDocument('renditions', $articleMedia);
                        foreach ($item->getRenditions() as $key => $rendition) {
                            /* @var $rendition Rendition */
                            $image = $this->objectManager->find(Image::class, $this->pathBuilder->build($this->mediaBasepath).'/'.$rendition->getMedia());
                            $imageRendition = new ImageRendition();
                            $imageRendition->setParent($renditionsDocument);
                            $imageRendition->setImage($image);
                            $imageRendition->setHeight($rendition->getHeight());
                            $imageRendition->setWidth($rendition->getWidth());
                            $imageRendition->setName($key);
                            $this->objectManager->persist($imageRendition);
                        }
                    } elseif ($item->getType() === 'file') {
                        //TODO: handle files upload
                    }
                    $this->objectManager->persist($articleMedia);
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
}
