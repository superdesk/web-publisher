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
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * Class TenantAwareThemeContext.
 */
final class TenantAwareThemeContext implements TenantAwareThemeContextInterface
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
    public function getTheme(): ?ThemeInterface
    {
        /* @var ThemeAwareTenantInterface $tenant */
        try {
            $tenant = $this->tenantContext->getTenant();
        } catch (TenantNotFoundException $e) {
            return null;
        }

        $key = md5($tenant->getCode().$tenant->getThemeName());
        if (array_key_exists($key, $this->themes)) {
            return $this->themes[$key];
        }

        if ($this->cacheService->contains('theme_'.$key)) {
            return $this->themes[$key] = $this->cacheService->fetch('theme_'.$key);
        }

        $themeName = $this->resolveThemeName($tenant);
        if (null === $themeName) {
            return null;
        }

        $theme = $this->themeRepository->findOneByName($themeName);
        unset($tenant);

        if (null === $theme) {
            return null;
        }

        $this->themes[$key] = $theme;
        $this->cacheService->save('theme_'.$key, $theme, 600);

        return $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveThemeName(ThemeAwareTenantInterface $tenant, string $themeName = null): ?string
    {
        if (null === $themeName) {
            $themeName = $tenant->getThemeName();
        }

        if (null !== $themeName) {
            return $themeName.ThemeHelper::SUFFIX_SEPARATOR.$tenant->getCode();
        }

        return $themeName;
    }
}
