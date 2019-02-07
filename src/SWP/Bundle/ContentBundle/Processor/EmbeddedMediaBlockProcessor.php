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
        if (ArticleInterface::KEY_FEATURE_MEDIA === $articleMedia->getKey() || null === $articleMedia->getImage()) {
            return;
        }

        $body = $article->getBody();
        $mediaId = str_replace('/', '\\/', $articleMedia->getKey());
        $assetId = $articleMedia->getImage()->getAssetId();
        $assetId = str_replace('_', '/', $assetId);

        $crawler = new Crawler();
        $crawler->addHtmlContent($body);
        $item = $crawler->filterXPath('//div[@class="media-block"]/img[contains(@src, "'.$assetId.'")]');

        $imgElement = $item->first()->getNode(0);
        if (null === $imgElement) {
            return;
        }

        $mediaBlockElement = $imgElement->parentNode;
        $captionText = $mediaBlockElement->getElementsByTagName('span')[0]->textContent;
        $editor3MediaBlock = $mediaBlockElement->ownerDocument->saveHTML($mediaBlockElement);
        $newNodeHtml = '<!-- EMBED START Image {id: "'.$mediaId.'"} --><figure><img src="'.$item->first()->attr('src').'" alt="'.$item->first()->attr('alt').'" /><figcaption>'.$captionText.'</figcaption></figure><!-- EMBED END Image {id: "'.$mediaId.'"} -->';

        $article->setBody(str_replace($editor3MediaBlock, $newNodeHtml, $crawler->html()));
    }

    public function supports(string $type): bool
    {
        return $this->fileExtensionChecker->isImage($type);
    }
}
