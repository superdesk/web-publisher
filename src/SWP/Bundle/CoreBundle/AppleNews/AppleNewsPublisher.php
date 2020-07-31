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

namespace SWP\Bundle\CoreBundle\AppleNews;

use SWP\Bundle\CoreBundle\AppleNews\Api\AppleNewsApi;
use SWP\Bundle\CoreBundle\AppleNews\Api\ClientFactory;
use SWP\Bundle\CoreBundle\AppleNews\Api\Response\AppleNewsArticle;
use SWP\Bundle\CoreBundle\AppleNews\Converter\ArticleToAppleNewsFormatConverter;
use SWP\Bundle\CoreBundle\Model\AppleNewsConfigInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Model\AppleNewsArticle as PublisherAppleNewsArticle;

class AppleNewsPublisher
{
    private $appleNewsConverter;

    public function __construct(ArticleToAppleNewsFormatConverter $appleNewsConverter)
    {
        $this->appleNewsConverter = $appleNewsConverter;
    }

    public function publish(ArticleInterface $article, TenantInterface $tenant): AppleNewsArticle
    {
        $appleNewsConfig = $tenant->getAppleNewsConfig();
        $appleNewsClient = $this->getClient($appleNewsConfig);
        $appleNewsArticle = $article->getAppleNewsArticle();

        $json = $this->appleNewsConverter->convert($article, $tenant);

        if (null === $appleNewsArticle) {
            $rawAppleNewsArticle = $appleNewsClient->createArticle($appleNewsConfig->getChannelId(), $json);

            $appleNewsArticle = new PublisherAppleNewsArticle();
            $appleNewsArticle->setShareUrl($rawAppleNewsArticle->getShareUrl());
            $appleNewsArticle->setArticleId($rawAppleNewsArticle->getArticleId());
            $appleNewsArticle->setRevisionId($rawAppleNewsArticle->getRevisionId());
            $article->setAppleNewsArticle($appleNewsArticle);

            return $rawAppleNewsArticle;
        }

        $rawAppleNewsArticle = $appleNewsClient->updateArticle(
            $appleNewsArticle->getArticleId(),
            $json,
            ['revision' => $appleNewsArticle->getRevisionId()]
        );

        $appleNewsArticle->setShareUrl($rawAppleNewsArticle->getShareUrl());
        $appleNewsArticle->setArticleId($rawAppleNewsArticle->getArticleId());
        $appleNewsArticle->setRevisionId($rawAppleNewsArticle->getRevisionId());

        return $rawAppleNewsArticle;
    }

    public function unpublish(ArticleInterface $article, TenantInterface $tenant): void
    {
        $appleNewsConfig = $tenant->getAppleNewsConfig();
        $appleNewsArticle = $article->getAppleNewsArticle();
        $appleNewsClient = $this->getClient($appleNewsConfig);

        $appleNewsClient->deleteArticle($appleNewsArticle->getArticleId());
    }

    private function getClient(AppleNewsConfigInterface $appleNewsConfig): AppleNewsApi
    {
        $clientFactory = new ClientFactory();

        return new AppleNewsApi($clientFactory->create(), $appleNewsConfig->getApiKeyId(), $appleNewsConfig->getApiKeySecret());
    }
}
