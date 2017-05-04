<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle;

final class MultiTenancyEvents
{
    /**
     * The TENANT_SET event occurs after setting tenant in context.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const TENANT_SET = 'swp.tenant.set';

    /**
     * The TENANTABLE_ENABLE event occurs on tenantable filter enable.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const TENANTABLE_ENABLE = 'swp.tenant.tenantable_enable';

    /**
     * The TENANTABLE_DISABLE event occurs on tenantable filter disable.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const TENANTABLE_DISABLE = 'swp.tenant.tenantable_disable';

    private function __construct()
    {
    }
}
