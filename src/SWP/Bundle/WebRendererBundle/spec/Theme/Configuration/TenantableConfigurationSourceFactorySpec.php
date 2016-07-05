<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\WebRendererBundle\Theme\Configuration;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\Configuration\TenantableConfigurationSourceFactory;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;

/**
 * @mixin TenantableConfigurationSourceFactory
 */
class TenantableConfigurationSourceFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantableConfigurationSourceFactory::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(ConfigurationSourceFactoryInterface::class);
    }

    public function it_should_return_name()
    {
        $this->getName()->shouldReturn('tenantable');
    }
}
