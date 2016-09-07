<?php

/**
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

namespace spec\SWP\Bundle\CoreBundle\Theme\Provider;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeProvider;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeProviderInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @mixin ThemeProvider
 */
class ThemeProviderSpec extends ObjectBehavior
{
    public function let(ThemeRepositoryInterface $themeRepository, TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($themeRepository, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeProvider::class);
    }

    public function it_has_an_interface()
    {
        $this->shouldImplement(ThemeProviderInterface::class);
    }

    public function it_gets_all_available_themes_for_current_tenant(
        ThemeInterface $theme1,
        ThemeInterface $theme2,
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        ThemeRepositoryInterface $themeRepository
    ) {
        $theme1->getName()->willReturn('swp/some-theme@123abc');
        $theme2->getName()->willReturn('swp/other-theme@zxc123');

        $themes = [
            'swp/some-theme@123abc' => $theme1,
            'swp/other-theme@zxc123' => $theme2,
        ];

        $themeRepository->findAll()->willReturn($themes);

        $tenant->getCode()->willReturn('123abc');
        $tenantContext->getTenant()->willReturn($tenant);

        $theme1->setName('swp/some-theme')->shouldBeCalled();

        $this->getCurrentTenantAvailableThemes()->shouldReturn([$theme1]);
    }
}
