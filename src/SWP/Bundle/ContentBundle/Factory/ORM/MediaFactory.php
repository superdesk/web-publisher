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

use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleMedia;
use SWP\Bundle\ContentBundle\Doctrine\ORM\File;
use SWP\Bundle\ContentBundle\Doctrine\ORM\Image;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaFactory implements MediaFactoryInterface
{
    public function create($article, $item)
    {
        $articleMedia = new ArticleMedia();
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);

        return $articleMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function createMediaAsset($uploadedFile, $assetId)
    {
        $asset = $this->getProperObject($uploadedFile);
        $asset->setAssetId($assetId);
        $asset->setFileExtension($uploadedFile->guessClientExtension());

        return $asset;
    }

    protected function getProperObject(UploadedFile $uploadedFile)
    {
        if (in_array(exif_imagetype($uploadedFile->getRealPath()), [
            IMAGETYPE_GIF,
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_BMP,
        ])) {
            return new Image();
        }

        return new File();
    }
}
