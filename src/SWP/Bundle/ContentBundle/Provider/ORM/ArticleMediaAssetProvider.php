<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use SWP\Bundle\ContentBundle\Doctrine\FileRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Bridge\Model\RenditionInterface;

class ArticleMediaAssetProvider implements ArticleMediaAssetProviderInterface
{
    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    public function __construct(ImageRepositoryInterface $imageRepository, FileRepositoryInterface $fileRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->fileRepository = $fileRepository;
    }

    public function getImage(RenditionInterface $rendition): ?ImageInterface
    {
        return $this->imageRepository->findImageByAssetId($this->getArticleMediaId($rendition));
    }

    public function getFile(RenditionInterface $rendition): ?FileInterface
    {
        return $this->fileRepository->findFileByAssetId($this->getArticleMediaId($rendition));
    }

    private function getArticleMediaId(RenditionInterface $rendition): string
    {
        return ArticleMedia::handleMediaId($rendition->getMedia());
    }
}
