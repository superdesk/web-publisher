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

use Psr\Log\LoggerInterface;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ArticleMediaAssetProviderInterface $articleMediaAssetProvider,
        FactoryInterface $factory,
        ImageRenditionFactoryInterface $imageRenditionFactory,
        MediaManagerInterface $mediaManager,
        LoggerInterface $logger
    ) {
        $this->articleMediaAssetProvider = $articleMediaAssetProvider;
        $this->factory = $factory;
        $this->imageRenditionFactory = $imageRenditionFactory;
        $this->mediaManager = $mediaManager;
        $this->logger = $logger;
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
            $this->logger->error(\sprintf('%s: %s', $rendition->getHref(), $e->getMessage()));

            return null;
        }
        /** @var Image $image */
        $image = $this->mediaManager->handleUploadedFile($uploadedFile, $rendition->getMedia());

        list($width, $height) = \getimagesize($uploadedFile->getRealPath());
        $image->setWidth($width);
        $image->setHeight($height);

        return $image;
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
