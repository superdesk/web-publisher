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
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

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

        $themes = $this->initializeThemes($configurations);
        $themes = $this->hydrateThemes($configurations, $themes);

        $this->checkForCircularDependencies($themes);

        return array_values($themes);
    }

    /**
     * @param array $configurations
     *
     * @return ThemeInterface[]
     */
    private function initializeThemes(array $configurations)
    {
        $themes = [];
        foreach ($configurations as $configuration) {
            /* @var ThemeInterface $theme */
            $themes[$configuration['name']] = $this->themeFactory->create($configuration['name'], $configuration['path']);
        }

        return $themes;
    }

    /**
     * @param array            $configurations
     * @param ThemeInterface[] $themes
     *
     * @return ThemeInterface[]
     */
    private function hydrateThemes(array $configurations, array $themes)
    {
        foreach ($configurations as $configuration) {
            $themeName = $configuration['name'];
            $configuration['parents'] = $this->convertParentsNamesToParentsObjects($themeName, $configuration['parents'], $themes);
            $configuration['authors'] = $this->convertAuthorsArraysToAuthorsObjects($configuration['authors']);
            $configuration['screenshots'] = $this->convertScreenshotsArraysToScreenshotsObjects($configuration['screenshots']);

            $theme = $themes[$configuration['name']];

            $theme->setTitle($configuration['title'] ?? null);
            $theme->setDescription($configuration['description'] ?? null);
            $theme->setName($configuration['name']);

            //$parentThemes = $this->convertParentsNamesToParentsObjects($configuration['name'], $configuration['parents'], $themes);
            foreach ($configuration['parents'] as $parentTheme) {
                $theme->addParent($parentTheme);
            }

            //$themeAuthors = $this->convertAuthorsArraysToAuthorsObjects($configuration['authors']);
            foreach ($configuration['authors'] as $themeAuthor) {
                $theme->addAuthor($themeAuthor);
            }

            //$themeScreenshots = $this->convertScreenshotsArraysToScreenshotsObjects($configuration['screenshots']);
            foreach ($configuration['screenshots'] as $themeScreenshot) {
                $theme->addScreenshot($themeScreenshot);
            }
            $themes[$themeName] = $theme;

            //$themes[$themeName] = $this->themeHydrator->hydrate($configuration, $themes[$themeName]);
        }

        return $themes;
    }

    /**
     * @param ThemeInterface[] $themes
     */
    private function checkForCircularDependencies(array $themes)
    {
        try {
            foreach ($themes as $theme) {
                $this->circularDependencyChecker->check($theme);
            }
        } catch (CircularDependencyFoundException $exception) {
            throw new ThemeLoadingFailedException('Circular dependency found.', 0, $exception);
        }
    }

    /**
     * @param string $themeName
     * @param array  $parentsNames
     * @param array  $existingThemes
     *
     * @return ThemeInterface[]
     */
    private function convertParentsNamesToParentsObjects($themeName, array $parentsNames, array $existingThemes)
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

    /**
     * @param array $authorsArrays
     *
     * @return ThemeAuthor[]
     */
    private function convertAuthorsArraysToAuthorsObjects(array $authorsArrays)
    {
        return array_map(function (array $authorArray) {
            return $this->themeAuthorFactory->createFromArray($authorArray);
        }, $authorsArrays);
    }

    /**
     * @param array $screenshotsArrays
     *
     * @return ThemeScreenshot[]
     */
    private function convertScreenshotsArraysToScreenshotsObjects(array $screenshotsArrays)
    {
        return array_map(function (array $screenshotArray) {
            return $this->themeScreenshotFactory->createFromArray($screenshotArray);
        }, $screenshotsArrays);
    }
}
