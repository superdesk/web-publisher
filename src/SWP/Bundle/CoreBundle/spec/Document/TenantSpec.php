<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Document;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\Route;
use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Model\Tenant as BaseTenant;

/**
 * @mixin Tenant
 */
class TenantSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Tenant::class);
        $this->shouldHaveType(BaseTenant::class);
    }

    public function it_implements_tenant_interface()
    {
        $this->shouldImplement(ThemeAwareTenantInterface::class);
        $this->shouldImplement(TenantInterface::class);
    }

    public function it_has_no_theme_by_default()
    {
        $this->getThemeName()->shouldReturn(null);
    }

    public function its_theme_is_mutable()
    {
        $this->setThemeName('theme-name');
        $this->getThemeName()->shouldReturn('theme-name');
    }

    public function it_has_no_homepage_by_default()
    {
        $this->getHomepage()->shouldReturn(null);
    }

    public function its_homepage_is_mutable(Route $homepage)
    {
        $this->setHomepage($homepage);
        $this->getHomepage()->shouldReturn($homepage);
    }
}
