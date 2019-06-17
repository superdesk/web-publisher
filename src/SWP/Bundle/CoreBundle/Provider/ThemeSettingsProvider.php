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

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\Common\Cache\CacheProvider;
use League\Flysystem\FilesystemInterface;
use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Provider\SettingsProviderInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

final class ThemeSettingsProvider implements SettingsProviderInterface
{
    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var string
     */
    private $themeConfigFilename;

    private $filesystem;

    private $cacheProvider;

    public function __construct(
        ThemeContextInterface $themeContext,
        string $themeConfigFileName,
        FilesystemInterface $filesystem,
        CacheProvider $cacheProvider
    ) {
        $this->themeContext = $themeContext;
        $this->themeConfigFilename = $themeConfigFileName;
        $this->filesystem = $filesystem;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        $currentTheme = $this->themeContext->getTheme();
        $themeConfigFile = $currentTheme->getPath().\DIRECTORY_SEPARATOR.$this->themeConfigFilename;

        if ($this->cacheProvider->contains(md5($themeConfigFile))) {
            $config = $this->cacheProvider->fetch(md5($themeConfigFile));
        } else {
            $content = $this->filesystem->read($themeConfigFile);
            $config = json_decode($content, true);
            $this->cacheProvider->save(md5($themeConfigFile), $config);
        }

        if (!isset($config['settings'])) {
            return [];
        }

        $settings = [];
        foreach ((array) $config['settings'] as $key => $value) {
            $value['scope'] = ScopeContextInterface::SCOPE_THEME;
            $settings[$key] = $value;
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(): bool
    {
        return null !== $this->themeContext->getTheme();
    }
}
