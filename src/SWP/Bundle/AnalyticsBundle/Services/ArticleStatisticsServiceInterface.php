<?php

declare(strict_types=1);

namespace SWP\Bundle\AnalyticsBundle\Services;

use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;

interface ArticleStatisticsServiceInterface
{
    public function addArticleEvent(int $articleId, string $action, array $extraData): ArticleStatisticsInterface;
}
