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

use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

class ThemeProvider implements ThemeProviderInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ThemeProvider constructor.
     *
     * @param ThemeRepositoryInterface $themeRepository
     * @param TenantContextInterface   $tenantContext
     */
    public function __construct(ThemeRepositoryInterface $themeRepository, TenantContextInterface $tenantContext)
    {
        $this->themeRepository = $themeRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentTenantAvailableThemes()
    {
        $themes = $this->themeRepository->findAll();

        return iterator_to_array($this->filterThemesByTenantCode($themes));
    }

    private function filterThemesByTenantCode(array $themes)
    {
        $currentTenantCode = $this->tenantContext->getTenant()->getCode();

        foreach ($themes as $key => $theme) {
            if (strpos($key, ThemeHelper::SUFFIX_SEPARATOR.$currentTenantCode) !== false) {
                $theme->setName(strstr($theme->getName(), ThemeHelper::SUFFIX_SEPARATOR, true));

                yield $theme;
            }
        }
    }
}
