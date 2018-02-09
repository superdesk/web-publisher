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

namespace SWP\Bundle\SettingsBundle\Context;

use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;

abstract class AbstractScopeContext implements ScopeContextInterface
{
    protected $scopeOwners = [];

    /**
     * {@inheritdoc}
     */
    public function getScopesOwners(): array
    {
        return $this->scopeOwners;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopeOwner(string $scope, SettingsOwnerInterface $owner)
    {
        if (\in_array($scope, $this->getScopes(), true)) {
            $this->scopeOwners[$scope] = $owner;

            return $owner;
        }
    }

    /**
     * @param string $scope
     *
     * @return SettingsOwnerInterface|bool
     */
    public function getScopeOwner(string $scope)
    {
        if (array_key_exists($scope, $this->scopeOwners)) {
            return $this->scopeOwners[$scope];
        }

        return null;
    }
}
