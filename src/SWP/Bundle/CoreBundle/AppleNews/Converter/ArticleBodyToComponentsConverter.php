<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Converter;

use SWP\Bundle\CoreBundle\AppleNews\Component\Body;
use SWP\Bundle\CoreBundle\AppleNews\Component\EmbedWebVideo;
use SWP\Bundle\CoreBundle\AppleNews\Component\FacebookPost;
use SWP\Bundle\CoreBundle\AppleNews\Component\Figure;
use SWP\Bundle\CoreBundle\AppleNews\Component\Heading;
use SWP\Bundle\CoreBundle\AppleNews\Component\Instagram;
use SWP\Bundle\CoreBundle\AppleNews\Component\Quote;
use SWP\Bundle\CoreBundle\AppleNews\Component\Tweet;

final class ArticleBodyToComponentsConverter
{
    public function convert(string $body): array
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.self::stripHtmlTags($body));
        $document->encoding = 'UTF-8';
        libxml_clear_errors();

        /** @var \DOMNodeList $body */
        if (!($body = $document->getElementsByTagName('body')->item(0))) {
            throw new \InvalidArgumentException('Invalid HTML was provided');
        }

        $components = [];
        foreach ($body->childNodes as $node) {
            switch ($node->nodeName) {
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                    if ('' !== $node->textContent) {
                        $level = substr($node->nodeName, 1);
                        $components[] = new Heading($node->textContent, (int) $level);
                    }

                    break;
                case 'p':
                    if ('' !== $node->textContent) {
                        $components[] = new Body($node->textContent, 'marginBetweenComponents');
                    }

                    break;

                case 'figure':
                    $src = $node->getElementsByTagName('img')
                        ->item(0)
                        ->getAttribute('src');

                    $caption = $node->getElementsByTagName('figcaption')
                        ->item(0)
                        ->textContent;

                    $components[] = new Figure($src, $caption);

                    break;
                case 'div':
                    if (!$node->hasAttribute('class')) {
                        break;
                    }

                    $iframeElement = $node->getElementsByTagName('iframe')
                        ->item(0);

                    if (null !== $iframeElement) {
                        $webVideoUrl = $iframeElement->getAttribute('src');
                        $url = str_replace('\"', '', $webVideoUrl);
                        if (false !== strpos($url, 'twitter.com')) {
                            $parsedUrl = parse_url($url);
                            parse_str($parsedUrl['query'], $url);

                            $components[] = new Tweet($url['url']);
                        } elseif (false !== strpos($url, 'iframe.ly')) {
                            $parsedUrl = parse_url($url);
                            parse_str($parsedUrl['query'], $url);

                            $components[] = new FacebookPost($url['url']);
                        } elseif (false !== strpos($url, 'facebook.com')) {
                            $parsedUrl = parse_url($url);
                            parse_str($parsedUrl['query'], $url);
                            if ($this->isValidFacebookPostUrl($url['href'])) {
                                $components[] = new FacebookPost($url['href']);
                            }
                        } elseif (false !== strpos($url, 'youtube.com') || false !== strpos($url, 'vimeo.com')) {
                            $components[] = new EmbedWebVideo($url);
                        }

                        break;
                    }

                    $instagramElement = $node->getElementsByTagName('blockquote')
                        ->item(0);

                    if (null !== $instagramElement) {
                        $instagramUrl = $instagramElement->getAttribute('data-instgrm-permalink');
                        $url = str_replace('\"', '', $instagramUrl);
                        $components[] = new Instagram($url);
                    }

                    break;

                case 'blockquote':
                    if ('' !== $node->textContent) {
                        $components[] = new Quote('“'.$node->textContent.'”');
                    }

                    break;
            }
        }

        return $components;
    }

    public static function stripHtmlTags(string $html): string
    {
        return preg_replace('/<script.*>.*<\/script>/isU', '', $html);
    }

    private function isValidFacebookPostUrl(string $url): bool
    {
        preg_match('/^https:\/\/www\.facebook\.com\/(photo(\.php|s)|permalink\.php|[^\/]+\/(activity|posts))[\/?].*$/', $url, $matches);

        return !empty($matches);
    }
}
