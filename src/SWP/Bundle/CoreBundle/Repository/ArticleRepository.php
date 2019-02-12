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
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\Common\Criteria\Criteria;

class ArticleRepository extends ContentBundleArticleRepository implements ArticleRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        $qb = parent::getByCriteria($criteria, $sorting)
            ->leftJoin('a.articleStatistics', 'stats')->addSelect('stats')
            ->leftJoin('a.externalArticle', 'ext')->addSelect('ext');

        return $qb;
    }

    public function getArticlesByCriteriaIds(Criteria $criteria): QueryBuilder
    {
        $queryBuilder = parent::getArticlesByCriteriaIds($criteria)
            ->leftJoin('a.articleStatistics', 'stats')->addSelect('stats')
            ->leftJoin('a.externalArticle', 'ext')->addSelect('ext');

        return $queryBuilder;
    }

    public function getArticleBySlugForPackage(string $slug, PackageInterface $package): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder
            ->where('a.slug = :slug')
                ->setParameter('slug', $slug)
            ->andWhere('a.package != :package')
                ->setParameter('package', $package)
        ;

        return $queryBuilder;
    }

    public function getArticlesByPackage(PackageInterface $package): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder
            ->andWhere('a.package = :package')
            ->setParameter('package', $package)
        ;

        return $queryBuilder;
    }

    public function getArticleByPackageExtraData(string $key, string $value): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder
            ->leftJoin('a.package', 'p')
            ->leftJoin('p.externalData', 'e')
            ->andWhere('e.key = :key')
            ->andWhere('e.value = :value')
            ->setParameters(['key' => $key, 'value' => $value])
        ;

        return $queryBuilder;
    }

    protected function applySorting(QueryBuilder $queryBuilder, array $sorting, string $alias, Criteria $criteria = null)
    {
        if (isset($sorting['pageViews']) && !empty($sorting['pageViews'])) {
            if ($criteria instanceof Criteria && null !== $dateRange = $criteria->get('dateRange', null)) {
                $startIsToday = date('Ymd') == date('Ymd', strtotime($dateRange[1]));
                $start = new \DateTime();
                $start->setTimestamp(strtotime($dateRange[0]));
                $start->setTime(23, 59, 59);
                $end = new \DateTime();
                $end->setTimestamp(strtotime($dateRange[1]));
                $end->setTime(0, 0, 0);

                $articleEventsQuery = $this->_em->createQueryBuilder()
                    ->from(ArticleEvent::class, 'ae')
                    ->select('COUNT(ae.id)')
                    ->andWhere('ae.createdAt >= :end')
                    ->andWhere('ae.articleStatistics = stats.id');

                $queryBuilder
                    ->addSelect(sprintf('(%s) as HIDDEN events_count', $articleEventsQuery))
                    ->setParameter('end', $end);

                if (!$startIsToday) {
                    $articleEventsQuery->andWhere('ae.createdAt <= :start');
                    $queryBuilder->setParameter('start', $start);
                }

                $queryBuilder->addOrderBy('events_count', $sorting['pageViews']);
            } else {
                $queryBuilder->addOrderBy($this->getPropertyName('pageViewsNumber', 'stats'), $sorting['pageViews']);
            }
            unset($sorting['pageViews']);
        }

        if (isset($sorting['commentsCount']) && !empty($sorting['commentsCount'])) {
            $queryBuilder->andWhere('a.commentsCount IS NOT NULL');
        }

        parent::applySorting($queryBuilder, $sorting, $alias);
    }
}
