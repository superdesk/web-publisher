<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\AnalyticsBundle\Repository\ArticleEventRepositoryInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * Class ArticleStatisticsService.
 */
class ArticleStatisticsService implements ArticleStatisticsServiceInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @var RepositoryInterface
     */
    protected $articleStatisticsRepository;

    /**
     * @var ArticleEventRepositoryInterface
     */
    protected $articleEventsRepository;

    /**
     * @var FactoryInterface
     */
    protected $articleStatisticsFactory;

    /**
     * @var FactoryInterface
     */
    protected $articleEventFactory;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        RepositoryInterface $articleStatisticsRepository,
        ArticleEventRepositoryInterface $articleEventsRepository,
        FactoryInterface $articleStatisticsFactory,
        FactoryInterface $articleEventFactory
    ) {
        $this->articleRepository = $articleRepository;
        $this->articleStatisticsRepository = $articleStatisticsRepository;
        $this->articleEventsRepository = $articleEventsRepository;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->articleEventFactory = $articleEventFactory;
    }

    public function addArticleEvent(int $articleId, string $action, array $extraData): ArticleStatisticsInterface
    {
        $articleStatistics = $this->getOrCreateNewArticleStatistics($articleId);
        switch ($action) {
            case ArticleEventInterface::ACTION_PAGEVIEW:
                $this->addNewPageViewEvent($articleStatistics, $articleId, $extraData[ArticleStatisticsServiceInterface::KEY_PAGEVIEW_SOURCE]);

                break;
            case ArticleEventInterface::ACTION_IMPRESSION:
                $sourceArticle = null;
                $sourceRoute = null;
                $type = null;
                if (array_key_exists(ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ARTICLE, $extraData)) {
                    $sourceArticle = $extraData[ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ARTICLE];
                }
                if (array_key_exists(ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ROUTE, $extraData)) {
                    $sourceRoute = $extraData[ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ROUTE];
                }
                if (array_key_exists(ArticleStatisticsServiceInterface::KEY_IMPRESSION_TYPE, $extraData)) {
                    $type = $extraData[ArticleStatisticsServiceInterface::KEY_IMPRESSION_TYPE];
                }
                $this->addNewImpressionEvent($articleStatistics, $articleId, $sourceArticle, $sourceRoute, $type);

                break;
        }

        return $articleStatistics;
    }

    protected function getOrCreateNewArticleStatistics(int $articleId): ArticleStatisticsInterface
    {
        /** @var ArticleStatisticsInterface $articleStatistics */
        $articleStatistics = $this->articleStatisticsRepository->findOneBy(['article' => $articleId]);
        if (null === $articleStatistics) {
            /** @var ArticleStatisticsInterface $articleStatistics */
            $articleStatistics = $this->articleStatisticsFactory->create();
        }

        return $articleStatistics;
    }

    protected function addNewPageViewEvent(ArticleStatisticsInterface $articleStatistics, int $articleId, string $pageViewSource): void
    {
        /** @var ArticleInterface $article */
        $article = $this->articleRepository->findOneBy(['id' => $articleId]);
        if (null === $article) {
            return;
        }

        $articleEvent = $this->getArticleEvent($articleStatistics, $article, ArticleEventInterface::ACTION_PAGEVIEW);
        $articleEvent->setPageViewSource($pageViewSource);
        $articleStatistics->increasePageViewsNumber();
        if (ArticleEventInterface::PAGEVIEW_SOURCE_INTERNAL === $pageViewSource) {
            $internalPageViewsCount = $this->articleEventsRepository->getCountForArticleInternalPageViews($article) + 1;
            if ($internalPageViewsCount > 0 && $articleStatistics->getImpressionsNumber() > 0) {
                $articleStatistics->setInternalClickRate(
                    \round($internalPageViewsCount / $articleStatistics->getImpressionsNumber(), 2)
                );
            } else {
                $articleStatistics->setInternalClickRate(0);
            }
        }
        $this->articleStatisticsRepository->add($articleStatistics);
    }

    protected function addNewImpressionEvent(
        ArticleStatisticsInterface $articleStatistics,
        int $articleId,
        ArticleInterface $sourceArticle = null,
        RouteInterface $sourceRoute = null,
        string $type = null
    ): void {
        /** @var ArticleInterface $article */
        $article = $this->articleRepository->findOneBy(['id' => $articleId]);
        if (null === $article) {
            return;
        }

        $articleEvent = $this->getArticleEvent($articleStatistics, $article, ArticleEventInterface::ACTION_IMPRESSION);
        $articleEvent->setImpressionArticle($sourceArticle);
        $articleEvent->setImpressionRoute($sourceRoute);
        $articleEvent->setImpressionType($type);
        $articleStatistics->increaseImpressionsNumber();
        $this->articleStatisticsRepository->add($articleStatistics);
    }

    private function getArticleEvent(
        ArticleStatisticsInterface $articleStatistics,
        ArticleInterface $article,
        string $action
    ): ArticleEventInterface {
        /** @var ArticleEventInterface $articleEvent */
        $articleEvent = $this->articleEventFactory->create();
        $articleEvent->setAction($action);
        $articleEvent->setArticleStatistics($articleStatistics);
        $this->articleStatisticsRepository->persist($articleEvent);
        $articleStatistics->addEvent($articleEvent);
        $articleStatistics->setArticle($article);

        return $articleEvent;
    }
}
