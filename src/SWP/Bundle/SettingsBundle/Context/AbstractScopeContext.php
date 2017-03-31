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
        if (in_array($scope, $this->getScopes())) {
            $this->scopeOwners[$scope] = $owner;
        }
    }
}
