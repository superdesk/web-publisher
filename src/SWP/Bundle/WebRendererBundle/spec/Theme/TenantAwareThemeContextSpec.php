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
namespace spec\SWP\Bundle\WebRendererBundle\Theme;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\TenantAwareThemeContext;
use SWP\Component\Common\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @mixin TenantAwareThemeContext
 */
class TenantAwareThemeContextSpec extends ObjectBehavior
{
    public function let(TenantContextInterface $tenantContext, ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($tenantContext, $themeRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantAwareThemeContext::class);
    }

    public function it_returns_a_theme(
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        ThemeInterface $theme,
        ThemeRepositoryInterface $themeRepository
    ) {
        $tenantContext->getTenant()->willReturn($tenant);
        $tenant->getSubdomain()->willReturn('subdomain1');
        $tenant->getThemeName()->willReturn('swp/default-theme');
        $themeRepository->findOneByName('swp/default-theme@subdomain1')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    public function it_throws_no_theme_exception_if_tenant_has_no_theme(
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        $themeRepository
    ) {
        $tenant->getSubdomain()->willReturn('subdomain');
        $tenant->getThemeName()->willReturn(null);
        $tenantContext->getTenant()->willReturn($tenant);
        $themeRepository->findOneByName(null)->shouldBeCalled()->willReturn(null);
        $this->shouldThrow('SWP\Bundle\WebRendererBundle\Exception\NoThemeException')->during('getTheme');
    }
}
