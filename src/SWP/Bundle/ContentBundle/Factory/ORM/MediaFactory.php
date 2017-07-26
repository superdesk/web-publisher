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
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\File;
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
use Symfony\Component\DomCrawler\Crawler;

class MediaFactory implements MediaFactoryInterface
{
    /**
     * @var ImageRepositoryInterface
     */
    protected $imageRepository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * MediaFactory constructor.
     *
     * @param ImageRepositoryInterface $imageRepository
     * @param FactoryInterface         $factory
     * @param FactoryInterface         $imageFactory
     * @param FactoryInterface         $fileFactory
     * @param MediaManagerInterface    $mediaManager
     */
    public function __construct(ImageRepositoryInterface $imageRepository, FactoryInterface $factory, FactoryInterface $imageFactory, FactoryInterface $fileFactory, MediaManagerInterface $mediaManager)
    {
        $this->imageRepository = $imageRepository;
        $this->factory = $factory;
        $this->mediaManager = $mediaManager;
        $this->imageFactory = $imageFactory;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ArticleInterface $article, string $key, ItemInterface $item): ArticleMediaInterface
    {
        $articleMedia = $this->factory->create();
        $articleMedia->setArticle($article);
        $articleMedia->setFromItem($item);
        $articleMedia = $this->createImageMedia($articleMedia, $key, $item);

        return $articleMedia;
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

        return $imageRendition;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceBodyImagesWithMedia(ArticleInterface $article, ArticleMediaInterface $articleMedia)
    {
        $body = $article->getBody();
        $mediaId = $articleMedia->getKey();
        preg_match(
            "/(<!-- EMBED START Image {id: \"$mediaId\"} -->)(.+?)(<!-- EMBED END Image {id: \"$mediaId\"} -->)/im",
            str_replace(PHP_EOL, '', $body),
            $embeds
        );

        if (empty($embeds)) {
            return;
        }

        $figureString = $embeds[2];
        $crawler = new Crawler($figureString);
        $images = $crawler->filter('figure img');
        /** @var \DOMElement $imageElement */
        foreach ($images as $imageElement) {
            foreach ($articleMedia->getRenditions() as $rendition) {
                if (strpos($imageElement->getAttribute('src'), ArticleMedia::getOriginalMediaId($rendition->getImage()->getAssetId())) !== false) {
                    $attributes = $imageElement->attributes;
                    while ($attributes->length) {
                        $imageElement->removeAttribute($attributes->item(0)->name);
                    }
                    $imageElement->setAttribute('src', $this->mediaManager->getMediaUri($rendition->getImage()));
                    $imageElement->setAttribute('data-media-id', $mediaId);
                    $imageElement->setAttribute('data-image-id', $rendition->getImage()->getAssetId());
                }
            }
        }

        $article->setBody(str_replace($figureString, $crawler->filter('body')->html(), $body));
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

        $originalRendition = $item->getRenditions()->filter(
            function (RenditionInterface $rendition) {
                return 'original' === $rendition->getName();
            }
        )->first();

        $articleMedia->setMimetype($originalRendition->getMimetype());
        $articleMedia->setKey($key);
        $image = $this->findImage($originalRendition->getMedia());
        $articleMedia->setImage($image);
        foreach ($item->getRenditions() as $rendition) {
            $image = $this->findImage($rendition->getMedia());
            if (null === $image) {
                continue;
            }

            $imageRendition = $this->createImageRendition($image, $articleMedia, $rendition->getName(), $rendition);
            $articleMedia->addRendition($imageRendition);
        }

        return $articleMedia;
    }

    protected function findImage(string $mediaId)
    {
        return $this->imageRepository->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));
    }
}
