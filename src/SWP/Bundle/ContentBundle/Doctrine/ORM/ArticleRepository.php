<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Event\Subscriber\Sortable\Doctrine\ORM\Query\OrderByWalker;
use Knp\Component\Pager\Pagination\PaginationInterface;
use SWP\Bundle\ContentBundle\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Pagination\PaginationData;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllArticles()
    {
        throw new \Exception('Not implemented');
    }

    public function getByCriteria(Criteria $criteria)
    {
        $qb = $this->createQueryBuilder('a');

        if ($criteria->has('id')) {
            $qb->andWhere('a.id = :id')
                ->setParameter('id', $criteria->get('id'));
        }

        if ($criteria->has('slug')) {
            $qb->andWhere('a.slug = :slug')
                ->setParameter('slug', $criteria->get('slug'));
        }

        if ($criteria->has('route')) {
            $qb->andWhere('a.route = :route')
                ->setParameter('route', $criteria->get('route'));
        }

        $qb->andWhere('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        $query = $qb->getQuery();

        $query->setFirstResult($criteria->get('firstResult', 0));
        if ($criteria->has('maxResults')) {
            if ($criteria->get('maxResults') === 0) {
                $query->setMaxResults($criteria->get('maxResults'));
            }
        } else {
            $query->setMaxResults(10);
        }

        return $query;
    }

    public function getPaginatedByCriteria(Criteria $criteria, PaginationData $paginationData): PaginationInterface
    {
        $criteria->set('firstResult', $paginationData->getFirstResult());
        $query = $this->getByCriteria($criteria);
        $query
            ->setHint(OrderByWalker::HINT_PAGINATOR_SORT_DIRECTION, $paginationData->getOrderDirection())
            ->setHint(OrderByWalker::HINT_PAGINATOR_SORT_FIELD, $paginationData->getOrderFields())
            ->setHint(OrderByWalker::HINT_PAGINATOR_SORT_FIELD, $paginationData->getOrderAliases());

        $paginator = new Paginator();

        return $paginator->paginate(
            $query,
            $paginationData->getPageNumber(),
            $paginationData->getLimit()
        );
    }

    /**
     * @param string $identifier
     * @param array  $order
     *
     * @return SqlQuery
     *
     * @throws \Exception
     */
    public function getQueryForRouteArticles(string $identifier, array $order = []) : SqlQuery
    {
        throw new \Exception('Not implemented');
    }
}
