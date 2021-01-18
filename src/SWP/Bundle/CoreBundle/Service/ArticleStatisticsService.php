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

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

class ArticleStatisticsService implements ArticleStatisticsServiceInterface
{
    protected $articleStatisticsRepository;

    protected $articleStatisticsFactory;

    protected $articleObjectManager;

    public function __construct(
        RepositoryInterface $articleStatisticsRepository,
        FactoryInterface $articleStatisticsFactory,
        ObjectManager $articleObjectManager
    ) {
        $this->articleStatisticsRepository = $articleStatisticsRepository;
        $this->articleStatisticsFactory = $articleStatisticsFactory;
        $this->articleObjectManager = $articleObjectManager;
    }

    public function addArticleEvent(int $articleId, string $action, array $extraData): ArticleStatisticsInterface
    {
        $articleStatistics = $this->getOrCreateNewArticleStatistics($articleId);
        if (ArticleEventInterface::ACTION_PAGEVIEW === $action) {
            $this->addNewPageViewEvent($articleStatistics, $articleId);
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

    protected function addNewPageViewEvent(ArticleStatisticsInterface $articleStatistics, int $articleId): void
    {
        /** @var ArticleInterface $article */
        $article = $this->articleObjectManager->getReference(ArticleInterface::class, $articleId);
        if (null === $article) {
            return;
        }
        $articleStatistics->setArticle($article);

        $this->articleStatisticsRepository->add($articleStatistics);
    }
}
