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

final class EmbeddedAudioProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    public function __construct(FileExtensionCheckerInterface $fileExtensionChecker, MediaManagerInterface $mediaManager)
    {
        $this->fileExtensionChecker = $fileExtensionChecker;
        $this->mediaManager = $mediaManager;
    }

    public function process(ArticleInterface $article, ArticleMediaInterface $articleMedia): void
    {
        $body = $article->getBody();

        preg_match(
            '/<audio[^>]*>/',
            str_replace(PHP_EOL, '', $body),
            $matches
        );

        if (empty($matches) || !isset($matches[0])) {
            return;
        }

        $audioString = $matches[0];
        $crawler = new Crawler($audioString);
        $audio = $crawler->filter('audio');

        /* @var \DOMElement $videoElement */
        foreach ($audio as $audioElement) {
            if (false !== strpos($audioElement->getAttribute('src'), ArticleMedia::getOriginalMediaId($articleMedia->getFile()->getAssetId()))) {
                $audioElement->setAttribute('src', $this->mediaManager->getMediaUri($articleMedia->getFile()));
            }
        }

        $article->setBody(str_replace($audioString, $crawler->filter('body')->html(), $body));
    }

    public function supports(string $type): bool
    {
        return $this->fileExtensionChecker->isAudio($type);
    }
}
