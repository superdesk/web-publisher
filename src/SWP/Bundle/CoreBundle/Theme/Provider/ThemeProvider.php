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

namespace SWP\Bundle\CoreBundle\Theme\Provider;

use function count;
use Generator;
use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

class ThemeProvider implements ThemeProviderInterface
{
    private $themeRepository;

    private $tenantContext;

    /**
     * Internal cache for loaded themes. It prevents multiple filtering themes by tenant code.
     */
    private $loadedThemes = [];

    public function __construct(ThemeRepositoryInterface $themeRepository, TenantContextInterface $tenantContext)
    {
        $this->themeRepository = $themeRepository;
        $this->tenantContext = $tenantContext;
    }

    public function getCurrentTenantAvailableThemes(): array
    {
        if (count($this->loadedThemes) > 0) {
            return $this->loadedThemes;
        }

        $themes = $this->themeRepository->findAll();
        $this->loadedThemes = iterator_to_array($this->filterThemesByTenantCode($themes));

        return $this->loadedThemes;
    }

    private function filterThemesByTenantCode(array $themes): ?Generator
    {
        $currentTenantCode = $this->tenantContext->getTenant()->getCode();

        foreach ($themes as $key => $theme) {
            $themeName = $theme->getName();
            if (false !== strpos($themeName, ThemeHelper::SUFFIX_SEPARATOR.$currentTenantCode)) {
                $theme->setName(strstr($themeName, ThemeHelper::SUFFIX_SEPARATOR, true));

                yield $theme;
            }
        }
    }
}
