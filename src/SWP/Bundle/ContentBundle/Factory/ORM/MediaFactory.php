<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\Image;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleMediaAssetProviderInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class MediaFactory implements MediaFactoryInterface
{
    /**
     * @var ArticleMediaAssetProviderInterface
     */
    protected $articleMediaAssetProvider;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ImageRenditionFactoryInterface
     */
    protected $imageRenditionFactory;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    public function __construct(
        ArticleMediaAssetProviderInterface $articleMediaAssetProvider,
        FactoryInterface $factory,
        ImageRenditionFactoryInterface $imageRenditionFactory,
        MediaManagerInterface $mediaManager
    ) {
        $this->articleMediaAssetProvider = $articleMediaAssetProvider;
        $this->factory = $factory;
        $this->imageRenditionFactory = $imageRenditionFactory;
        $this->mediaManager = $mediaManager;
    }

    public function create(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia = $this->factory->create();
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);

        if (ItemInterface::TYPE_PICTURE === $item->getType()) {
            return $this->createImageMedia($articleMedia, $key, $item);
        }

        return $this->createFileMedia($articleMedia, $key, $item);
    }

    public function createEmpty(): ArticleMediaInterface
    {
        return $this->factory->create();
    }

    protected function createFileMedia(ArticleMedia $articleMedia, string $key, ItemInterface $item): ArticleMediaInterface
    {
        if (0 === $item->getRenditions()->count()) {
            return $articleMedia;
        }

        $originalRendition = $this->findOriginalRendition($item);

        $articleMedia->setMimetype($originalRendition->getMimetype());
        $articleMedia->setKey($key);

        $file = $this->articleMediaAssetProvider->getFile($originalRendition);
        $articleMedia->setFile($file);

        return $articleMedia;
    }

    protected function createImageMedia(ArticleMedia $articleMedia, string $key, ItemInterface $item): ArticleMediaInterface
    {
        if (0 === $item->getRenditions()->count()) {
            return $articleMedia;
        }

        $originalRendition = $this->findOriginalRendition($item);

        $articleMedia->setMimetype($originalRendition->getMimetype());
        $articleMedia->setKey($key);

        $image = $this->getImage($originalRendition);
        $articleMedia->setImage($image);

        foreach ($item->getRenditions() as $rendition) {
            $image = $this->getImage($rendition);

            if (null === $image) {
                continue;
            }

            $imageRendition = $this->imageRenditionFactory->createWith($articleMedia, $image, $rendition);
            $articleMedia->addRendition($imageRendition);
        }

        return $articleMedia;
    }

    public function getImage(RenditionInterface $rendition): ?ImageInterface
    {
        $file = $this->articleMediaAssetProvider->getImage($rendition);
        if (null !== $file) {
            return $file;
        }

        try {
            $uploadedFile = $this->mediaManager->downloadFile(
                $rendition->getHref(),
                $rendition->getMedia(),
                $rendition->getMimetype()
            );
        } catch (\Exception $e) {
            // problem with file download - ignore it
            return null;
        }
        /** @var Image $file */
        $file = $this->mediaManager->handleUploadedFile($uploadedFile, $rendition->getMedia());
        $file->setWidth($rendition->getWidth());
        $file->setHeight($rendition->getHeight());

        return $file;
    }

    private function findOriginalRendition(ItemInterface $item): RenditionInterface
    {
        return $item->getRenditions()->filter(
            function (RenditionInterface $rendition) {
                return 'original' === $rendition->getName();
            }
        )->first();
    }
}
