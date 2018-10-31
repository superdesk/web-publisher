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
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\Image;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\Rendition;
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

    public function __construct(
        ImageRepositoryInterface $imageRepository,
        FileRepositoryInterface $fileRepository,
        FactoryInterface $factory
    ) {
        $this->imageRepository = $imageRepository;
        $this->fileRepository = $fileRepository;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function createEmpty(): ArticleMediaInterface
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createImageRendition(
        ImageInterface $image,
        ArticleMediaInterface $articleMedia,
        string $key,
        Rendition $rendition
    ): ImageRenditionInterface {
        $imageRendition = new ImageRendition();
        $imageRendition->setImage($image);
        $imageRendition->setMedia($articleMedia);
        $imageRendition->setHeight($rendition->getHeight());
        $imageRendition->setWidth($rendition->getWidth());
        $imageRendition->setName($key);
        $imageRendition->setPreviewUrl($rendition->getHref());

        return $imageRendition;
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

        return $articleMedia;
    }

    /**
     * Handle Article Media with Image (add renditions, set mimetype etc.).
     *
     * @param ArticleMedia  $articleMedia
     * @param string        $key          unique key shared between media and image rendition
     * @param ItemInterface $item
     *
     * @return ArticleMedia
     */
    protected function createImageMedia(ArticleMedia $articleMedia, string $key, ItemInterface $item)
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
                $image = new Image();
            }

            $imageRendition = $this->createImageRendition($image, $articleMedia, $rendition->getName(), $rendition);
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

    protected function findImage(string $mediaId)
    {
        return $this->imageRepository->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));
    }
}
