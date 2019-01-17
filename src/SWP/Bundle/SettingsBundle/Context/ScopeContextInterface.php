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

interface ScopeContextInterface
{
    const SCOPE_GLOBAL = 'global';

    const SCOPE_USER = 'user';

    public function getScopes(): array;

    public function getScopesOwners(): array;

    /**
     * @return null|SettingsOwnerInterface
     */
    public function setScopeOwner(string $scope, SettingsOwnerInterface $owner);

    /**
     * @return null|SettingsOwnerInterface
     */
    public function getScopeOwner(string $scope);
}
