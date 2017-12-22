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

namespace SWP\Bundle\AnalyticsBundle\Services;

use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
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
     * @var FactoryInterface
     */
    protected $articleStatisticsFactory;

    /**
     * @var FactoryInterface
     */
    protected $articleEventFactory;

    /**
     * ArticleStatisticsService constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param RepositoryInterface        $articleStatisticsRepository
     * @param FactoryInterface           $articleStatisticsFactory
     * @param FactoryInterface           $articleEventFactory
     */
    public function __construct(ArticleRepositoryInterface $articleRepository, RepositoryInterface $articleStatisticsRepository, FactoryInterface $articleStatisticsFactory, FactoryInterface $articleEventFactory)
    {
        $this->articleRepository = $articleRepository;
        $this->articleStatisticsRepository = $articleStatisticsRepository;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->articleEventFactory = $articleEventFactory;
    }

    /**
     * @param int    $articleId
     * @param string $action
     *
     * @return ArticleStatisticsInterface
     */
    public function addArticleEvent(int $articleId, string $action): ArticleStatisticsInterface
    {
        $articleStatistics = $this->getOrCreateNewArticleStatistics($articleId);
        switch ($action) {
            case ArticleEventsInterface::ACTION_PAGEVIEW:
                $this->addNewPageViewEvent($articleStatistics, $articleId);

                break;
        }

        return $articleStatistics;
    }

    /**
     * @param int $articleId
     *
     * @return mixed
     */
    protected function getOrCreateNewArticleStatistics(int $articleId)
    {
        $articleStatistics = $this->articleStatisticsRepository->findOneBy(['article' => $articleId]);
        if (null === $articleStatistics) {
            $articleStatistics = $this->articleStatisticsFactory->create();
        }

        return $articleStatistics;
    }

    /**
     * @param ArticleStatisticsInterface $articleStatistics
     * @param int                        $articleId
     */
    protected function addNewPageViewEvent(ArticleStatisticsInterface $articleStatistics, int $articleId)
    {
        /** @var ArticleInterface $article */
        $article = $this->articleRepository->findOneBy(['id' => $articleId]);
        /** @var ArticleEventsInterface $articleEvent */
        $articleEvent = $this->articleEventFactory->create();
        $articleEvent->setAction(ArticleEventsInterface::ACTION_PAGEVIEW);
        $articleEvent->setArticleStatistics($articleStatistics);
        $this->articleStatisticsRepository->persist($articleEvent);
        $articleStatistics->addEvent($articleEvent);
        $articleStatistics->setArticle($article);
        $articleStatistics->increasePageViewsNumber();
        $this->articleStatisticsRepository->add($articleStatistics);
    }
}
