<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Context;

use SWP\Bundle\SettingsBundle\Context\ScopeContext as BaseScopeContext;

class ScopeContext extends BaseScopeContext implements ScopeContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScopes(): array
    {
        return array_merge(parent::getScopes(), [
            ScopeContextInterface::SCOPE_ORGANIZATION,
            ScopeContextInterface::SCOPE_TENANT,
        ]);
    }
}
