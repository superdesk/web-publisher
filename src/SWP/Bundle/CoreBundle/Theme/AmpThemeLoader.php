<?php

declare(strict_types=1);

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

use SWP\Bundle\CoreBundle\Theme\Loader\NamespacedFilesystemTemplateLoader;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Takeit\Bundle\AmpHtmlBundle\Loader\ThemeLoaderInterface;

final class AmpThemeLoader implements ThemeLoaderInterface
{
    private $filesystemTemplateLoader;

    private $themeContext;

    private $themePath;

    private $themeHierarchyProvider;

    private $themeAssetProvider;

    public function __construct(
        NamespacedFilesystemTemplateLoader $filesystemTemplateLoader,
        ThemeContextInterface $themeContext,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        string $themePath,
        ThemeAssetProviderInterface $themeAssetProvider
    ) {
        $this->filesystemTemplateLoader = $filesystemTemplateLoader;
        $this->themeContext = $themeContext;
        $this->themePath = $themePath;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $themes = $this->themeHierarchyProvider->getThemeHierarchy(
            $this->themeContext->getTheme()
        );

        foreach ($themes as $theme) {
            $directoryPath = sprintf('%s/%s', $theme->getPath(), trim($this->themePath, '/'));
            if ($this->themeAssetProvider->hasFile($directoryPath)) {
                $this->filesystemTemplateLoader->addPath(
                    $directoryPath,
                    ThemeLoaderInterface::THEME_NAMESPACE
                );
            }
        }
    }
}
