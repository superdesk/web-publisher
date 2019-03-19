<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Model\DateTime;
use SWP\Bundle\ContentBundle\Service\ArticleService as BaseArticleService;

class ArticleService extends BaseArticleService
{
    public function publish(ArticleInterface $article): ArticleInterface
    {
        if (null === $article->getPublishedAt()) {
            $article->setPublishedAt(DateTime::getCurrentDateTime());
        }

        return parent::publish($article);
    }
}
