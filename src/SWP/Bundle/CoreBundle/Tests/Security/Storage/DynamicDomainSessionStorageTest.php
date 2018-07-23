<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\CoreBundle\Model\Organization;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Security\Storage\DynamicDomainSessionStorage;
use SWP\Bundle\FixturesBundle\WebTestCase;

class DynamicDomainSessionStorageTest extends WebTestCase
{
    public function testSettingOptionsInSessionNStorage()
    {
        $tenant = new Tenant();
        $tenant->setDomainName('testing.dev');
        $tenant->setOrganization(new Organization());

        $tenantContext = $this->getContainer()->get('swp_multi_tenancy.tenant_context');
        $tenantContext->setTenant($tenant);

        new DynamicDomainSessionStorage($tenantContext);

        self::assertEquals('.testing.dev', ini_get('session.cookie_domain'));
        self::assertEquals(true, ini_get('session.cookie_httponly'));
    }
}
