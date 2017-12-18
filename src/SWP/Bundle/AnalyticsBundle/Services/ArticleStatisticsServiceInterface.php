<?php

declare(strict_types=1);

namespace SWP\Bundle\AnalyticsBundle\Services;

use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;

/**
 * Interface ArticleStatisticsServiceInterface.
 */
interface ArticleStatisticsServiceInterface
{
    /**
     * @param int    $articleId
     * @param string $action
     *
     * @return ArticleStatisticsInterface
     */
    public function addArticleEvent(int $articleId, string $action): ArticleStatisticsInterface;
}
