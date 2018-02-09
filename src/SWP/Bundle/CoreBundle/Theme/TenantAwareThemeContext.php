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

namespace SWP\Bundle\CoreBundle\Theme;

use Doctrine\Common\Cache\CacheProvider;
use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
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
     * @var ReloadableThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var CacheProvider
     */
    private $cacheService;

    /**
     * @var array
     */
    private $themes;

    /**
     * TenantAwareThemeContext constructor.
     *
     * @param TenantContextInterface   $tenantContext   Tenant context
     * @param ThemeRepositoryInterface $themeRepository Theme repository
     * @param CacheProvider            $cacheService    Cache Service
     */
    public function __construct(TenantContextInterface $tenantContext, ThemeRepositoryInterface $themeRepository, CacheProvider $cacheService)
    {
        $this->tenantContext = $tenantContext;
        $this->themeRepository = $themeRepository;
        $this->cacheService = $cacheService;
        $this->themes = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        /* @var ThemeAwareTenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();
        if (null === $tenant) {
            return;
        }

        $key = md5($tenant->getCode().$tenant->getThemeName());
        if (array_key_exists($key, $this->themes)) {
            return $this->themes[$key];
        }

        if ($this->cacheService->contains('theme_'.$key)) {
            return $this->themes[$key] = $this->cacheService->fetch('theme_'.$key);
        }

        $theme = $this->themeRepository->findOneByName($this->resolveThemeName($tenant));
        unset($tenant);

        if (null === $theme) {
            return null;
        }

        $this->themes[$key] = $theme;
        $this->cacheService->save('theme_'.$key, $theme, 600);

        return $theme;
    }

    /**
     * @param ThemeAwareTenantInterface $tenant
     *
     * @return string
     */
    private function resolveThemeName(ThemeAwareTenantInterface $tenant)
    {
        $themeName = $tenant->getThemeName();
        if (null !== $themeName) {
            return $tenant->getThemeName().ThemeHelper::SUFFIX_SEPARATOR.$tenant->getCode();
        }

        return $themeName;
    }
}
