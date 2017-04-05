<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Model;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository implements SettingsRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllByScopeAndOwner(ScopeContextInterface $scopeContext): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :globalScope')
            ->setParameter('globalScope', ScopeContextInterface::SCOPE_GLOBAL);

        /* @var array $scope */
        foreach ($scopeContext->getScopesOwners() as $scopeName => $owner) {
            $qb->orWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('s.scope', ':scope_'.$scopeName),
                    $qb->expr()->eq('s.owner', ':owner_'.$scopeName)
                )
            )
            ->setParameter('scope_'.$scopeName, $scopeName)
            ->setParameter('owner_'.$scopeName, $owner->getId());
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByNameAndScopeAndOwner(string $name, string $scope, SettingsOwnerInterface $owner = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.scope = :scope')
            ->setParameter('scope', $scope)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name);

        if (null !== $scope && null !== $owner) {
            $qb
                ->andWhere('s.owner = :owner')
                ->setParameter('owner', $owner->getId())
            ;
        }

        return $qb;
    }
}
