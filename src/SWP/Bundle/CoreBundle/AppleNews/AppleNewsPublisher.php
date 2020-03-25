<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews;

use SWP\Bundle\CoreBundle\AppleNews\Api\AppleNewsApi;
use SWP\Bundle\CoreBundle\AppleNews\Api\Response\AppleNewsArticle;
use SWP\Bundle\CoreBundle\AppleNews\Converter\ArticleToAppleNewsFormatConverter;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;

class AppleNewsPublisher
{
    private $appleNewsConverter;

    private $appleNewsClient;

    public function __construct(ArticleToAppleNewsFormatConverter $appleNewsConverter, AppleNewsApi $appleNewsClient)
    {
        $this->appleNewsConverter = $appleNewsConverter;
        $this->appleNewsClient = $appleNewsClient;
    }

    public function publish(ArticleInterface $article, TenantInterface $tenant): void
    {
        $json = $this->appleNewsConverter->convert($article);

        /** @var AppleNewsArticle $appleNewsArticle */
        $rawAppleNewsArticle = $this->appleNewsClient->createArticle('channelid', $json);

        $appleNewsArticle = new \SWP\Bundle\CoreBundle\Model\AppleNewsArticle();

        $appleNewsArticle->setArticle($article);
        $appleNewsArticle->setShareUrl($rawAppleNewsArticle->getShareUrl());
        $appleNewsArticle->setArticleId($rawAppleNewsArticle->getArticleId());
        $appleNewsArticle->setRevisionId($rawAppleNewsArticle->getRevisionId());

        $article->setAppleNewsArticle($appleNewsArticle);
    }
}
