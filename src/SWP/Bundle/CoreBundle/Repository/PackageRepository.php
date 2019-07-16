<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository as BasePackageRepository;
use SWP\Component\Common\Criteria\Criteria;

class PackageRepository extends BasePackageRepository implements PackageRepositoryInterface
{
    public function applyCriteria(QueryBuilder $queryBuilder, Criteria $criteria, string $alias)
    {
        if ($criteria->has('authors')) {
            $queryBuilder->leftJoin($alias.'.authors', 'au');
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('authors') as $value) {
                $orX->add($queryBuilder->expr()->eq('au.name', $queryBuilder->expr()->literal($value)));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('authors');
        }

        if ($criteria->has('article-body-content')) {
            $value = $criteria->get('article-body-content');
            $queryBuilder->leftJoin($alias.'.articles', 'a');

            $orX = $queryBuilder->expr()->orX();
            $orX->add($queryBuilder->expr()->like('a.body', $queryBuilder->expr()->literal('%'.$value.'%')));
            $orX->add($queryBuilder->expr()->like('a.lead', $queryBuilder->expr()->literal('%'.$value.'%')));

            $queryBuilder->andWhere($orX);
            $criteria->remove('article-body-content');
        }

        if ($criteria->has('statuses')) {
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('statuses') as $value) {
                $orX->add($queryBuilder->expr()->eq($alias.'.status', $queryBuilder->expr()->literal($value)));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('statuses');
        }

        parent::applyCriteria($queryBuilder, $criteria, $alias);
    }
}
