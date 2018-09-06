<?php

declare(strict_types=1);

namespace SWP\Bundle\AnalyticsBundle\Services;

use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;

interface ArticleStatisticsServiceInterface
{
    const KEY_PAGEVIEW_SOURCE = 'pageViewSource';

    const KEY_IMPRESSION_SOURCE_ARTICLE = 'sourceArticle';
    const KEY_IMPRESSION_SOURCE_ROUTE = 'sourceRoute';
    const KEY_IMPRESSION_TYPE = 'type';

    public function addArticleEvent(int $articleId, string $action, array $extraData): ArticleStatisticsInterface;
}
