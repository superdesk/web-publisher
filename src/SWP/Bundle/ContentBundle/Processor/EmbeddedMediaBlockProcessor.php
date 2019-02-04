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
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use Symfony\Component\DomCrawler\Crawler;

final class EmbeddedMediaBlockProcessor implements ArticleBodyProcessorInterface
{
    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    public function __construct(FileExtensionCheckerInterface $fileExtensionChecker)
    {
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    public function process(ArticleInterface $article, ArticleMediaInterface $articleMedia): void
    {
        if (ArticleInterface::KEY_FEATURE_MEDIA === $articleMedia->getKey()) {
            return;
        }

        $body = $article->getBody();
        $mediaId = str_replace('/', '\\/', $articleMedia->getKey());

        preg_match(
            '/(<div class="media-block">)(.+?)(<\/div>)/im',
            str_replace(PHP_EOL, '', $body),
            $embeds
        );

        if (empty($embeds)) {
            return;
        }

        [$mediaBlockDiv, $div, $figureString] = $embeds;

        $crawler = new Crawler($figureString);
        $src = $crawler->filterXPath('//img')->attr('src');
        $alt = $crawler->filterXPath('//img')->attr('alt');
        $captionNode = $crawler->filterXPath('//span[@class="media-block__description"]')->first();
        $caption = $captionNode->getNode(0)->textContent;

        $html = '<!-- EMBED START Image {id: "'.$mediaId.'"} --><figure><img src="'.$src.'" alt="'.$alt.'"/><figcaption>'.$caption.'</figcaption></figure><!-- EMBED END Image {id: "'.$mediaId.'"} -->';

        $article->setBody(str_replace($mediaBlockDiv, $html, $body));
    }

    public function supports(string $type): bool
    {
        return $this->fileExtensionChecker->isImage($type);
    }
}
