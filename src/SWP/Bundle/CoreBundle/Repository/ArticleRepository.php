<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleRepository as ContentBundleArticleRepository;
use SWP\Bundle\CoreBundle\Model\ArticleEvent;
use SWP\Component\Common\Criteria\Criteria;

/**
 * Class ArticleRepository.
 */
class ArticleRepository extends ContentBundleArticleRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        $qb = parent::getByCriteria($criteria, $sorting);
        $qb->leftJoin('a.articleStatistics', 'stats')->addSelect('stats');

        return $qb;
    }

    /**
     * @param Criteria $criteria
     *
     * @return QueryBuilder
     */
    public function getArticlesByCriteriaIds(Criteria $criteria): QueryBuilder
    {
        $queryBuilder = parent::getArticlesByCriteriaIds($criteria)
            ->addSelect('stats')
            ->leftJoin('a.articleStatistics', 'stats');

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting, string $alias, Criteria $criteria = null)
    {
        if (isset($sorting['pageViews']) && !empty($sorting['pageViews'])) {
            if ($criteria instanceof Criteria && null !== $dateRange = $criteria->get('dateRange', null)) {
                list($start, $end) = $dateRange;
                $articleEventsQuery = $this->_em->createQueryBuilder()
                    ->from(ArticleEvent::class, 'ae')
                    ->select('COUNT(ae.id)')
                    ->where('ae.createdAt >= :start')
                    ->andWhere('ae.createdAt <= :end');

                $queryBuilder
                    ->addSelect(sprintf('(%s) as HIDDEN events_count', $articleEventsQuery))
                    ->setParameter('start', $start)
                    ->setParameter('end', $end);
                $queryBuilder->addOrderBy('events_count', $sorting['pageViews']);
            } else {
                $queryBuilder->addOrderBy($this->getPropertyName('pageViewsNumber', 'stats'), $sorting['pageViews']);
            }
            unset($sorting['pageViews']);
        }

        return parent::applySorting($queryBuilder, $sorting, $alias);
    }
}
