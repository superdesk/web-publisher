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
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
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
            ->andWhere('a.status = :status')
            ->setParameters(['key' => $key, 'value' => $value, 'status' => ArticleInterface::STATUS_PUBLISHED])
        ;

        return $queryBuilder;
    }

    public function getArticleByExtraData(string $key, string $value): QueryBuilder
    {
        $criteria = new Criteria([
            'maxResults' => 1,
            'extra' => [
                $key => $value,
            ],
            'status' => ArticleInterface::STATUS_PUBLISHED,
        ]);

        return $this->getArticlesByCriteria($criteria);
    }

    protected function applySorting(QueryBuilder $queryBuilder, array $sorting, string $alias, Criteria $criteria = null)
    {
        $properties = \array_merge($this->getClassMetadata()->getFieldNames(), $this->getClassMetadata()->getAssociationNames());
        foreach ($sorting as $property => $order) {
            if ('pageViews' === $property && !empty($order)) {
                if ($criteria instanceof Criteria && null !== $dateRange = $criteria->get('dateRange', null)) {
                    $start = new \DateTime();
                    $start->setTimestamp(strtotime($dateRange[0]));
                    $start->setTime(23, 59, 59);
                    $end = new \DateTime();
                    $end->setTimestamp(strtotime($dateRange[1]));
                    $end->setTime(0, 0, 0);

                    $queryBuilder
                        ->andWhere('a.publishedAt <= :start')
                        ->andWhere('a.publishedAt >= :end')
                        ->setParameter('start', $start)
                        ->setParameter('end', $end);
                }

                $queryBuilder->addOrderBy($this->getPropertyName('pageViewsNumber', 'stats'), $sorting['pageViews']);

                unset($sorting['pageViews']);

                continue;
            }

            if (!\in_array($property, $properties)) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property, $alias), $order);
                unset($sorting[$property]);
            }
        }
    }
}
