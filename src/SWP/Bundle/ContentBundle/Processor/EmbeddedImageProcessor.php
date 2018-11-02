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

namespace SWP\Bundle\ContentBundle\Processor;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use Symfony\Component\DomCrawler\Crawler;

final class EmbeddedImageProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    public function __construct(MediaManagerInterface $mediaManager, FileExtensionCheckerInterface $fileExtensionChecker)
    {
        $this->mediaManager = $mediaManager;
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    public function process(ArticleInterface $article, ArticleMediaInterface $articleMedia): void
    {
        $body = $article->getBody();
        $mediaId = str_replace('/', '\\/', $articleMedia->getKey());
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
                if (false !== strpos($imageElement->getAttribute('src'), ArticleMedia::getOriginalMediaId($rendition->getImage()->getAssetId()))) {
                    $attributes = $imageElement->attributes;
                    $altAttribute = null;
                    if ($imageElement->hasAttribute('alt')) {
                        $altAttribute = $attributes->getNamedItem('alt');
                    }

                    while ($attributes->length) {
                        $imageElement->removeAttribute($attributes->item(0)->name);
                    }

                    $imageElement->setAttribute('src', $this->mediaManager->getMediaUri($rendition->getImage()));
                    $imageElement->setAttribute('data-media-id', $mediaId);
                    $imageElement->setAttribute('data-image-id', $rendition->getImage()->getAssetId());
                    if (null !== $altAttribute) {
                        $imageElement->setAttribute('alt', $altAttribute->nodeValue);
                    }
                }
            }
        }

        $article->setBody(str_replace($figureString, $crawler->filter('body')->html(), $body));
    }

    public function supports(string $type): bool
    {
        return $this->fileExtensionChecker->isImage($type);
    }
}
