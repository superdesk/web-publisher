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

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Takeit\Bundle\AmpHtmlBundle\Loader\ThemeLoaderInterface;
use Twig\Loader\FilesystemLoader;

final class AmpThemeLoader implements ThemeLoaderInterface
{
    /**
     * @var FilesystemLoader
     */
    private $filesystem;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var string
     */
    private $themePath;

    /**
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * AmpThemeLoader constructor.
     *
     * @param FilesystemLoader         $filesystem
     * @param ThemeContextInterface           $themeContext
     * @param ThemeHierarchyProviderInterface $themeHierarchyProvider
     * @param string                          $themePath
     */
    public function __construct(
        FilesystemLoader $filesystem,
        ThemeContextInterface $themeContext,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        string $themePath
    ) {
        $this->filesystem = $filesystem;
        $this->themeContext = $themeContext;
        $this->themePath = $themePath;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
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
            if (file_exists($directoryPath)) {
                $this->filesystem->addPath(
                    $directoryPath,
                    ThemeLoaderInterface::THEME_NAMESPACE
                );
            }
        }
    }
}
