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

use SWP\Bundle\SettingsBundle\Provider\SettingsProviderInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

final class ThemeSettingsProvider implements SettingsProviderInterface
{
    private $themeContext;
    private $themeConfigFilename;

    public function __construct(
        ThemeContextInterface $themeContext,
        string $themeConfigFileName
    ) {
        $this->themeContext = $themeContext;
        $this->themeConfigFilename = $themeConfigFileName;
    }

    public function getSettings(): array
    {
        $currentTheme = $this->themeContext->getTheme();
        $content = file_get_contents($currentTheme->getPath().\DIRECTORY_SEPARATOR.$this->themeConfigFilename);
        $config = json_decode($content, true);

        if (!isset($config['settings'])) {
            return [];
        }

        $settings = [];
        foreach ((array) $config['settings'] as $key => $value) {
            $value['scope'] = 'theme';
            $settings[$key] = $value;
        }

        return $settings;
    }

    public function supports(): bool
    {
        return null !== $this->themeContext->getTheme();
    }
}
