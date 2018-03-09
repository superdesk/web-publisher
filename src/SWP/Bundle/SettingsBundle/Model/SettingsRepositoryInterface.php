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
use SWP\Component\Storage\Repository\RepositoryInterface;

interface SettingsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $scope
     */
    public function removeAllByScope(string $scope): void;

    /**
     * @param ScopeContextInterface $scopeContext
     *
     * @return QueryBuilder
     */
    public function findAllByScopeAndOwner(ScopeContextInterface $scopeContext): QueryBuilder;

    /**
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return QueryBuilder
     */
    public function findOneByNameAndScopeAndOwner(string $name, string $scope, SettingsOwnerInterface $owner = null): QueryBuilder;

    /**
     * @param string                 $scope
     * @param SettingsOwnerInterface $settingsOwner
     *
     * @return QueryBuilder
     */
    public function findByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): QueryBuilder;
}
