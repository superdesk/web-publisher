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

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 */

namespace SWP\Bundle\CoreBundle\Theme\Repository;

use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 */
final class ReloadableInMemoryRepository implements ReloadableThemeRepositoryInterface
{
    /**
     * @var ThemeInterface[]
     */
    private $themes = [];

    /**
     * @var ThemeLoaderInterface
     */
    private $themeLoader;

    /**
     * @var bool
     */
    private $themesLoaded = false;

    /**
     * @param ThemeLoaderInterface $themeLoader
     */
    public function __construct(ThemeLoaderInterface $themeLoader)
    {
        $this->themeLoader = $themeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function reloadThemes(): void
    {
        $this->themesLoaded = false;
        $this->loadThemesIfNeeded();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $this->loadThemesIfNeeded();

        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName(string $name): ?ThemeInterface
    {
        $this->loadThemesIfNeeded();

        return isset($this->themes[$name]) ? $this->themes[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTitle(string $title): ?ThemeInterface
    {
        $this->loadThemesIfNeeded();

        foreach ($this->themes as $theme) {
            if ($theme->getTitle() === $title) {
                return $theme;
            }
        }

        return null;
    }

    private function loadThemesIfNeeded()
    {
        if ($this->themesLoaded) {
            return;
        }

        $themes = $this->themeLoader->load();
        foreach ($themes as $theme) {
            $this->themes[$theme->getName()] = $theme;
        }

        $this->themesLoaded = true;
    }
}
