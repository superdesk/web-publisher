<?php

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

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\Rendition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaFactoryInterface
{
    /**
     * @param ArticleInterface $article
     * @param string           $key
     * @param ItemInterface    $item
     *
     * @return ArticleMediaInterface
     */
    public function create(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface;

    /**
     * @param UploadedFile $uploadedFile
     * @param string       $assetId
     *
     * @return FileInterface
     */
    public function createMediaAsset(UploadedFile $uploadedFile, string $assetId): FileInterface;

    /**
     * @param ImageInterface        $image
     * @param ArticleMediaInterface $articleMedia
     * @param string                $key
     * @param Rendition             $rendition
     *
     * @return ImageRenditionInterface
     */
    public function createImageRendition(
        ImageInterface $image,
        ArticleMediaInterface $articleMedia,
        string $key, Rendition $rendition
    ): ImageRenditionInterface;
}
