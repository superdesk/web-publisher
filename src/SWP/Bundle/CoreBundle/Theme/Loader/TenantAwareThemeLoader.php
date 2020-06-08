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

namespace SWP\Bundle\CoreBundle\Theme\Loader;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeAuthorFactoryInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeScreenshotFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 */
final class TenantAwareThemeLoader implements ThemeLoaderInterface
{
    /**
     * @var ConfigurationProviderInterface
     */
    private $configurationProvider;

    /**
     * @var ThemeFactoryInterface
     */
    private $themeFactory;

    /**
     * @var ThemeAuthorFactoryInterface
     */
    private $themeAuthorFactory;

    /**
     * @var ThemeScreenshotFactoryInterface
     */
    private $themeScreenshotFactory;

    /**
     * @var CircularDependencyCheckerInterface
     */
    private $circularDependencyChecker;

    /**
     * @param ConfigurationProviderInterface     $configurationProvider
     * @param ThemeFactoryInterface              $themeFactory
     * @param ThemeAuthorFactoryInterface        $themeAuthorFactory
     * @param ThemeScreenshotFactoryInterface    $themeScreenshotFactory
     * @param CircularDependencyCheckerInterface $circularDependencyChecker
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeAuthorFactoryInterface $themeAuthorFactory,
        ThemeScreenshotFactoryInterface $themeScreenshotFactory,
        CircularDependencyCheckerInterface $circularDependencyChecker
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->themeFactory = $themeFactory;
        $this->themeAuthorFactory = $themeAuthorFactory;
        $this->themeScreenshotFactory = $themeScreenshotFactory;
        $this->circularDependencyChecker = $circularDependencyChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function load(): array
    {
        $configurations = $this->configurationProvider->getConfigurations();

        $themes = $this->hydrateThemes($configurations);

        $this->checkForCircularDependencies($themes);

        return array_values($themes);
    }

    private function hydrateThemes(array $configurations): array
    {
        $themes = [];

        foreach ($configurations as $configuration) {
            $themes[$configuration['name']] = $this->themeFactory->create($configuration['name'], $configuration['path']);
        }

        foreach ($configurations as $configuration) {
            $theme = $themes[$configuration['name']];

            $theme->setTitle($configuration['title'] ?? null);
            $theme->setDescription($configuration['description'] ?? null);

            $parentThemes = $this->convertParentsNamesToParentsObjects($configuration['name'], $configuration['parents'], $themes);
            foreach ($parentThemes as $parentTheme) {
                $theme->addParent($parentTheme);
            }

            $themeAuthors = $this->convertAuthorsArraysToAuthorsObjects($configuration['authors']);
            foreach ($themeAuthors as $themeAuthor) {
                $theme->addAuthor($themeAuthor);
            }

            $themeScreenshots = $this->convertScreenshotsArraysToScreenshotsObjects($configuration['screenshots']);
            foreach ($themeScreenshots as $themeScreenshot) {
                $theme->addScreenshot($themeScreenshot);
            }
        }

        return $themes;
    }

    /**
     * @param ThemeInterface[] $themes
     */
    private function checkForCircularDependencies(array $themes): void
    {
        try {
            foreach ($themes as $theme) {
                $this->circularDependencyChecker->check($theme);
            }
        } catch (CircularDependencyFoundException $exception) {
            throw new ThemeLoadingFailedException('Circular dependency found.', 0, $exception);
        }
    }

    private function convertParentsNamesToParentsObjects($themeName, array $parentsNames, array $existingThemes): array
    {
        $tenantCode = substr($themeName, strpos($themeName, '@') + 1);

        return array_map(function ($parentName) use ($themeName, $existingThemes, $tenantCode) {
            $parentName .= '@'.$tenantCode;
            if (!isset($existingThemes[$parentName])) {
                throw new ThemeLoadingFailedException(sprintf(
                    'Unexisting theme "%s" is required by "%s".',
                    $parentName,
                    $themeName
                ));
            }

            return $existingThemes[$parentName];
        }, $parentsNames);
    }

    private function convertAuthorsArraysToAuthorsObjects(array $authorsArrays): array
    {
        return array_map(function (array $authorArray) {
            return $this->themeAuthorFactory->createFromArray($authorArray);
        }, $authorsArrays);
    }

    private function convertScreenshotsArraysToScreenshotsObjects(array $screenshotsArrays): array
    {
        return array_map(function (array $screenshotArray) {
            return $this->themeScreenshotFactory->createFromArray($screenshotArray);
        }, $screenshotsArrays);
    }
}
