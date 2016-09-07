<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Factory;

use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * Interface TenantFactoryInterface.
 */
interface TenantFactoryInterface extends FactoryInterface
{
    /**
     * Creates a new tenant for given organization code.
     *
     * @param string $code
     *
     * @return TenantInterface
     */
    public function createForOrganization($code);
}
