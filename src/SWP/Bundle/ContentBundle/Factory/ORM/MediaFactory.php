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

use SWP\Bundle\ContentBundle\Doctrine\FileRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\File;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class MediaFactory implements MediaFactoryInterface
{
    /**
     * @var ImageRepositoryInterface
     */
    protected $imageRepository;

    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ImageRenditionFactoryInterface
     */
    protected $imageRenditionFactory;

    /**
     * @var FactoryInterface
     */
    protected $imageFactory;

    public function __construct(
        ImageRepositoryInterface $imageRepository,
        FileRepositoryInterface $fileRepository,
        FactoryInterface $factory,
        ImageRenditionFactoryInterface $imageRenditionFactory,
        FactoryInterface $imageFactory
    ) {
        $this->imageRepository = $imageRepository;
        $this->fileRepository = $fileRepository;
        $this->factory = $factory;
        $this->imageRenditionFactory = $imageRenditionFactory;
        $this->imageFactory = $imageFactory;
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
        $file = $this->findFile($originalRendition->getMedia());
        $articleMedia->setFile($file);

        if (null === $file) {
            $file = new File();
            $file->setAssetId($originalRendition->getMedia());
            $file->setPreviewUrl($originalRendition->getHref());
        }

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

        $image = $this->findImage($originalRendition->getMedia());
        $articleMedia->setImage($image);

        foreach ($item->getRenditions() as $rendition) {
            $image = $this->findImage($rendition->getMedia());

            if (null === $image) {
                $image = $this->imageFactory->create();
                $image->setAssetId($rendition->getMedia());
            }

            $imageRendition = $this->imageRenditionFactory->createWith($articleMedia, $image, $rendition);
            $articleMedia->addRendition($imageRendition);
        }

        return $articleMedia;
    }

    private function findOriginalRendition(ItemInterface $item): RenditionInterface
    {
        return $item->getRenditions()->filter(
            function (RenditionInterface $rendition) {
                return 'original' === $rendition->getName();
            }
        )->first();
    }

    protected function findFile(string $mediaId): ?FileInterface
    {
        return $this->fileRepository->findFileByAssetId(ArticleMedia::handleMediaId($mediaId));
    }

    protected function findImage(string $mediaId): ?ImageInterface
    {
        return $this->imageRepository->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));
    }
}
