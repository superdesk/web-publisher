<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Repository;

use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleEventRepository extends EntityRepository implements ArticleEventRepositoryInterface
{
    public function getCountForArticleInternalPageViews(ArticleInterface $article): int
    {
        $qb = $this->createQueryBuilder('ae')
            ->select('COUNT(ae.id)')
            ->andWhere('ae.action = :action')
            ->andWhere('ae.pageViewSource = :pageviewSource')
            ->leftJoin('ae.articleStatistics', 'ast')
            ->andWhere('ast.article = :article')
            ->setParameters([
                'article' => $article,
                'action' => ArticleEventInterface::ACTION_PAGEVIEW,
                'pageviewSource' => ArticleEventInterface::PAGEVIEW_SOURCE_INTERNAL,
            ]);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
