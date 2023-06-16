<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Provider\ORM\ArticleMediaAssetProviderInterface;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContextInterface;
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ArticleMediaAssetProvider implements ArticleMediaAssetProviderInterface
{
    /**
     * @var ArticleMediaAssetProviderInterface
     */
    private $decoratedArticleMediaAssetProvider;

    /**
     * @var ArticlePreviewContextInterface
     */
    private $articlePreviewContext;

    /**
     * @var FileFactoryInterface
     */
    private $fileFactory;

    /**
     * @var FactoryInterface
     */
    private $imageFactory;

    public function __construct(
        ArticleMediaAssetProviderInterface $decoratedArticleMediaAssetProvider,
        ArticlePreviewContextInterface $articlePreviewContext,
        FileFactoryInterface $fileFactory,
        FactoryInterface $imageFactory
    ) {
        $this->decoratedArticleMediaAssetProvider = $decoratedArticleMediaAssetProvider;
        $this->articlePreviewContext = $articlePreviewContext;
        $this->fileFactory = $fileFactory;
        $this->imageFactory = $imageFactory;
    }

    public function getImage(RenditionInterface $rendition): ?ImageInterface
    {
        if ($this->articlePreviewContext->isPreview()) {
            $image = $this->imageFactory->create();
            $image->setAssetId($rendition->getMedia());
            $this->setFileExtension($image, $rendition);

            return $image;
        }

        return $this->decoratedArticleMediaAssetProvider->getImage($rendition);
    }

    public function getFile(RenditionInterface $rendition): ?FileInterface
    {
        if ($this->articlePreviewContext->isPreview()) {
            $file = $this->fileFactory->createFile();
            $file->setAssetId($rendition->getMedia());
            $file->setPreviewUrl($rendition->getHref());
            $this->setFileExtension($file, $rendition);

            return $file;
        }

        return $this->decoratedArticleMediaAssetProvider->getFile($rendition);
    }

    private function setFileExtension(FileInterface $file, RenditionInterface $rendition): FileInterface
    {
        if (null === $rendition->getMimetype()) {
            return $file;
        }

        try {
            $extension = MimeTypeHelper::getExtensionByMimeType($rendition->getMimetype());
        } catch (\Exception $e) {
            return $file;
        }

        if (!empty($extension) && null === $file->getFileExtension()) {
            $file->setFileExtension($extension);
        }

        return $file;
    }
}
