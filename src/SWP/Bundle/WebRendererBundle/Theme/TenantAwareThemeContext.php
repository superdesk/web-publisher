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
namespace SWP\Bundle\WebRendererBundle\Theme;

use SWP\Bundle\WebRendererBundle\Theme\Helper\ThemeHelper;
use SWP\Component\Common\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * Class TenantAwareThemeContext.
 */
final class TenantAwareThemeContext implements ThemeContextInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * TenantAwareThemeContext constructor.
     *
     * @param TenantContextInterface   $tenantContext   Tenant context
     * @param ThemeRepositoryInterface $themeRepository Theme repository
     */
    public function __construct(
        TenantContextInterface $tenantContext,
        ThemeRepositoryInterface $themeRepository
    ) {
        $this->tenantContext = $tenantContext;
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        /* @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();

        return $this->themeRepository->findOneByName($this->resolveThemeName($tenant));
    }

    private function resolveThemeName(TenantInterface $tenant)
    {
        $themeName = $tenant->getThemeName();
        if (null !== $themeName) {
            return $tenant->getThemeName().ThemeHelper::SUFFIX_SEPARATOR.$tenant->getSubdomain();
        }

        return $themeName;
    }
}
