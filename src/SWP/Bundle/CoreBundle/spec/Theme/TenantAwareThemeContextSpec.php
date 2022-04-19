<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Theme;

use Symfony\Component\Cache\Adapter\NullAdapter;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use SWP\Bundle\CoreBundle\Theme\TenantAwareThemeContext;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin TenantAwareThemeContext
 */
class TenantAwareThemeContextSpec extends ObjectBehavior
{
    public function let(
        TenantContextInterface $tenantContext,
        ReloadableThemeRepositoryInterface $themeRepository
    ) {
        $this->beConstructedWith($tenantContext, $themeRepository, new NullAdapter());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantAwareThemeContext::class);
    }

    public function it_returns_a_theme(
        TenantContextInterface $tenantContext,
        ThemeAwareTenantInterface $tenant,
        ThemeInterface $theme,
        ReloadableThemeRepositoryInterface $themeRepository
    ) {
        $tenantContext->getTenant()->willReturn($tenant);
        $tenant->getSubdomain()->willReturn('subdomain1');
        $tenant->getCode()->willReturn('code');
        $tenant->getThemeName()->willReturn('swp/default-theme');
        $themeRepository->findOneByName('swp/default-theme@code')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    public function it_throws_no_theme_exception_if_tenant_has_no_theme(
        TenantContextInterface $tenantContext,
        ThemeAwareTenantInterface $tenant,
        $themeRepository
    ) {
        $tenant->getSubdomain()->willReturn('subdomain');
        $tenant->getCode()->willReturn('code');
        $tenant->getThemeName()->willReturn(null);
        $tenantContext->getTenant()->willReturn($tenant);
        $themeRepository->findOneByName(null)->shouldNotBeCalled();
        $this->getTheme()->shouldReturn(null);
    }
}
