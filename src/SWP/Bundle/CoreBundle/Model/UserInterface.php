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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Bundle\UserBundle\Model\UserInterface as BaseUserInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

interface UserInterface extends BaseUserInterface, TenantAwareInterface, SettingsOwnerInterface
{
    const ROLE_READER = 'ROLE_READER';
}
