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

class ScopeContext extends AbstractScopeContext implements ScopeContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScopes(): array
    {
        return [
            ScopeContextInterface::SCOPE_GLOBAL,
            ScopeContextInterface::SCOPE_USER,
        ];
    }
}
