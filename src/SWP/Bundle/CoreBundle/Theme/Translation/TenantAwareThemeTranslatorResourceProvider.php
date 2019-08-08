<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Translation;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Finder\TranslationFilesFinderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\ThemeTranslationResource;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

final class TenantAwareThemeTranslatorResourceProvider implements TranslatorResourceProviderInterface
{
    private $translationFilesFinder;

    private $themeRepository;

    private $themeHierarchyProvider;

    private $tenantContext;

    public function __construct(
        TranslationFilesFinderInterface $translationFilesFinder,
        ThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        TenantContextInterface $tenantContext
    ) {
        $this->translationFilesFinder = $translationFilesFinder;
        $this->themeRepository = $themeRepository;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(): array
    {
        /** @var ThemeInterface[] $themes */
        $themes = $this->themeRepository->findAll();
        $resources = [];
        foreach ($themes as $theme) {
            if (null !== $this->tenantContext->getTenant() && !strpos($theme->getName(), $this->tenantContext->getTenant()->getCode())) {
                continue;
            }

            $resources = array_merge($resources, $this->extractResourcesFromTheme($theme));
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesLocales(): array
    {
        return array_values(array_unique(array_map(function (TranslationResourceInterface $translationResource): string {
            return $translationResource->getLocale();
        }, $this->getResources())));
    }

    private function extractResourcesFromTheme(ThemeInterface $mainTheme): array
    {
        /** @var ThemeInterface[] $themes */
        $themes = array_reverse($this->themeHierarchyProvider->getThemeHierarchy($mainTheme));

        $resources = [];
        foreach ($themes as $theme) {
            $paths = $this->translationFilesFinder->findTranslationFiles($theme->getPath());

            foreach ($paths as $path) {
                $resources[] = new ThemeTranslationResource($mainTheme, $path);
            }
        }

        return $resources;
    }
}
