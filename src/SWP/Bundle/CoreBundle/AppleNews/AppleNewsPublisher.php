<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews;

use SWP\Bundle\CoreBundle\AppleNews\Api\AppleNewsApi;
use SWP\Bundle\CoreBundle\AppleNews\Api\ClientFactory;
use SWP\Bundle\CoreBundle\AppleNews\Converter\ArticleToAppleNewsFormatConverter;
use SWP\Bundle\CoreBundle\Model\AppleNewsArticle;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;

class AppleNewsPublisher
{
    private $appleNewsConverter;

    public function __construct(ArticleToAppleNewsFormatConverter $appleNewsConverter)
    {
        $this->appleNewsConverter = $appleNewsConverter;
    }

    public function publish(ArticleInterface $article, TenantInterface $tenant): void
    {
        $appleNewsConfig = $tenant->getAppleNewsConfig();

        $clientFactory = new ClientFactory();
        $appleNewsClient = new AppleNewsApi($clientFactory->create(), $appleNewsConfig->getApiKeyId(), $appleNewsConfig->getApiKeySecret());

        $json = $this->appleNewsConverter->convert($article);
        $rawAppleNewsArticle = $appleNewsClient->createArticle($appleNewsConfig->getChannelId(), $json);

        $appleNewsArticle = new AppleNewsArticle();
        $appleNewsArticle->setArticle($article);
        $appleNewsArticle->setShareUrl($rawAppleNewsArticle->getShareUrl());
        $appleNewsArticle->setArticleId($rawAppleNewsArticle->getArticleId());
        $appleNewsArticle->setRevisionId($rawAppleNewsArticle->getRevisionId());

        $article->setAppleNewsArticle($appleNewsArticle);
    }
}
