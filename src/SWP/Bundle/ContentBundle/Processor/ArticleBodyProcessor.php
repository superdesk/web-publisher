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

namespace SWP\Bundle\ContentBundle\Processor;

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ArticleBodyProcessor.
 */
class ArticleBodyProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * MediaFactory constructor.
     *
     * @param MediaManagerInterface $mediaManager
     */
    public function __construct(MediaManagerInterface $mediaManager)
    {
        $this->mediaManager = $mediaManager;
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
}
