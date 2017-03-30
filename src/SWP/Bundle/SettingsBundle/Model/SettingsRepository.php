<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Model;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository implements SettingsRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllByScopeAndOwner(string $scope = null, SettingsOwnerInterface $owner = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');
        if (null !== $scope) {
            $qb->andWhere('s.scope = :scope')->setParameter('scope', $scope);
        }

        return $qb;
    }

    public function findOneByNameAndScopeAndOwner(string $name, string $scope, SettingsOwnerInterface $owner = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :scope')
            ->setParameter('scope', $scope)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name);

        return $qb;
    }
}
