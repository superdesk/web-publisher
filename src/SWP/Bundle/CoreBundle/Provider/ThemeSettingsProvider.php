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

    /**
     * ThemeSettingsProvider constructor.
     *
     * @param ThemeContextInterface $themeContext
     * @param string                $themeConfigFileName
     */
    public function __construct(
        ThemeContextInterface $themeContext,
        string $themeConfigFileName
    ) {
        $this->themeContext = $themeContext;
        $this->themeConfigFilename = $themeConfigFileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        $currentTheme = $this->themeContext->getTheme();
        $themeConfigFile = $currentTheme->getPath().\DIRECTORY_SEPARATOR.$this->themeConfigFilename;
        $content = file_get_contents($themeConfigFile);
        $config = json_decode($content, true);

        if (!isset($config['settings'])) {
            throw new \InvalidArgumentException(sprintf('Settings ("settings" key) not set in %s', $themeConfigFile));
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
