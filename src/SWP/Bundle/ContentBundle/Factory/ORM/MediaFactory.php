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

use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\File;
use SWP\Bundle\ContentBundle\Model\Image;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\Rendition;
use SWP\Component\Common\Criteria\Criteria;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaFactory implements MediaFactoryInterface
{
    /**
     * @var ImageRepositoryInterface
     */
    protected $imageRepository;

    /**
     * @var string
     */
    protected $mediaModelClass;

    /**
     * MediaFactory constructor.
     *
     * @param ImageRepositoryInterface $imageRepository
     * @param string                   $mediaModelClass
     */
    public function __construct(
        ImageRepositoryInterface $imageRepository,
        string $mediaModelClass
    ) {
        $this->imageRepository = $imageRepository;
        $this->mediaModelClass = $mediaModelClass;
    }

    public function create(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia = new $this->mediaModelClass();
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);

        $articleMedia = $this->createImageMedia($articleMedia, $key, $item);

        return $articleMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function createMediaAsset(UploadedFile $uploadedFile, string $assetId): FileInterface
    {
        $asset = $this->getProperObject($uploadedFile);
        $asset->setAssetId($assetId);
        $asset->setFileExtension($uploadedFile->guessClientExtension());

        return $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function createImageRendition(
        ImageInterface $image,
        ArticleMediaInterface $articleMedia,
        string $key, Rendition $rendition
    ): ImageRenditionInterface {
        $imageRendition = new ImageRendition();
        $imageRendition->setImage($image);
        $imageRendition->setMedia($articleMedia);
        $imageRendition->setHeight($rendition->getHeight());
        $imageRendition->setWidth($rendition->getWidth());
        $imageRendition->setName($key);

        return $imageRendition;
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

        $originalRendition = $item->getRenditions()['original'];
        $criteria = new Criteria();
        $criteria->set('assetId', ArticleMedia::handleMediaId($originalRendition->getMedia()));

        $articleMedia->setMimetype($originalRendition->getMimetype());
        $articleMedia->setKey($key);
        $image = $this->imageRepository->getByCriteria($criteria, [])->getQuery()->getOneOrNullResult();
        $articleMedia->setImage($image);

        foreach ($item->getRenditions() as $key => $rendition) {
            $criteria->set('assetId', ArticleMedia::handleMediaId($rendition->getMedia()));
            $image = $this->imageRepository->getByCriteria($criteria, [])->getQuery()->getOneOrNullResult();
            if (null === $image) {
                continue;
            }

            $imageRendition = $image->getRendition();
            if (null === $imageRendition) {
                $imageRendition = $this->createImageRendition($image, $articleMedia, $key, $rendition);
                $this->imageRepository->persist($imageRendition);
            }

            $articleMedia->addRendition($imageRendition);
        }

        return $articleMedia;
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
