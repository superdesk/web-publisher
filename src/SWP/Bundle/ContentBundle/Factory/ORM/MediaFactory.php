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
use Sentry\Breadcrumb;
use Sentry\State\HubInterface;
use SWP\Bundle\ContentBundle\File\FileDownloaderInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleMediaAssetProviderInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class MediaFactory implements MediaFactoryInterface
{
    protected $articleMediaAssetProvider;

    protected $factory;

    protected $imageRenditionFactory;

    protected $mediaManager;

    protected $logger;

    protected $fileDownloader;

    private $sentryHub;

    public function __construct(
        ArticleMediaAssetProviderInterface $articleMediaAssetProvider,
        FactoryInterface $factory,
        ImageRenditionFactoryInterface $imageRenditionFactory,
        MediaManagerInterface $mediaManager,
        LoggerInterface $logger,
        FileDownloaderInterface $fileDownloader,
        HubInterface $sentryHub
    ) {
        $this->articleMediaAssetProvider = $articleMediaAssetProvider;
        $this->factory = $factory;
        $this->imageRenditionFactory = $imageRenditionFactory;
        $this->mediaManager = $mediaManager;
        $this->logger = $logger;
        $this->fileDownloader = $fileDownloader;
        $this->sentryHub = $sentryHub;
    }

    public function create(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        /** @var ArticleMediaInterface $articleMedia */
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

    protected function createFileMedia(ArticleMediaInterface $articleMedia, string $key, ItemInterface $item): ArticleMediaInterface
    {
        if (0 === $item->getRenditions()->count()) {
            return $articleMedia;
        }

        $originalRendition = $this->findOriginalRendition($item);
        $articleMedia->setMimetype($originalRendition->getMimetype());
        $articleMedia->setKey($key);
        $file = $this->articleMediaAssetProvider->getFile($originalRendition);
        $articleMedia->setFile($this->getFile($originalRendition, $file));

        return $articleMedia;
    }

    protected function createImageMedia(ArticleMediaInterface $articleMedia, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia->setKey($key);
        $articleMedia->setMimetype('unknown');
        if (0 === $item->getRenditions()->count()) {
            return $articleMedia;
        }

        $originalRendition = $this->findOriginalRendition($item);
        $articleMedia->setMimetype($originalRendition->getMimetype());
        /** @var ImageInterface $image */
        $image = $this->getFile($originalRendition, $this->articleMediaAssetProvider->getImage($originalRendition));
        if (!$image instanceof ImageInterface) {
            return $articleMedia;
        }
        $articleMedia->setImage($image);

        foreach ($item->getRenditions() as $itemRendition) {
            $image = $this->getFile($itemRendition, $this->articleMediaAssetProvider->getImage($itemRendition));
            if (null === $image || !$image instanceof ImageInterface) {
                continue;
            }

            $articleMedia->addRendition($this->imageRenditionFactory->createWith($articleMedia, $image, $itemRendition));
        }

        return $articleMedia;
    }

    private function getFile(RenditionInterface $rendition, ?FileInterface $file): ?FileInterface
    {
        if (null !== $file) {
            return $file;
        }

        try {
            return $this->downloadAsset($rendition->getHref(), $rendition->getMedia(), $rendition->getMimetype());
        } catch (\Exception $e) {
            $this->logException($e, $rendition);

            return null;
        }
    }

    private function downloadAsset(string $url, string $media, string $mimetype): FileInterface
    {
        $this->logger->info(\sprintf('Downloading %s for media %s', $url, $media));
        $uploadedFile = $this->fileDownloader->download($url, $media, $mimetype);
        $file = $this->mediaManager->handleUploadedFile($uploadedFile, $media);

        if ($file instanceof ImageInterface) {
            [$width, $height] = \getimagesize($uploadedFile->getRealPath());
            $file->setWidth($width);
            $file->setHeight($height);
            $size = \filesize($uploadedFile->getRealPath());
            $size = $size / 1024;
            $size = (string) number_format($size);
            if (null !== $size) {
                $file->setLength($size);
            }
        }

        return $file;
    }

    private function findOriginalRendition(ItemInterface $item): RenditionInterface
    {
        return $item->getRenditions()->filter(
            static function (RenditionInterface $rendition) {
                return 'original' === $rendition->getName();
            }
        )->first();
    }

    private function logException(\Exception $e, RenditionInterface $rendition): void
    {
        $this->logger->error(\sprintf('%s: %s', $rendition->getHref(), $e->getMessage()), ['trace' => $e->getTraceAsString()]);
        $this->sentryHub->addBreadcrumb(new Breadcrumb(
            Breadcrumb::LEVEL_DEBUG,
            Breadcrumb::TYPE_DEFAULT,
            'publishing',
            'Media',
            [
                'rendition id' => $rendition->getId(),
                'rendition media' => $rendition->getMedia(),
            ]
        ));
        $this->sentryHub->captureException($e);
    }
}
