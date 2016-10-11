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

use SWP\Bundle\ContentBundle\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy([
            'slug' => $slug
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

        $query = $qb->getQuery();

        if ($criteria->has('maxResults')) {
            if ($criteria->get('maxResults') === 0) {
                $query->setMaxResults($criteria->get('maxResults'));
            }
        } else {
            $query->setMaxResults(10);
        }

        return $query;
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
