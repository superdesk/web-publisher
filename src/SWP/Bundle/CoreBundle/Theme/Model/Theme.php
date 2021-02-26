<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Theme\Model;

use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use Sylius\Bundle\ThemeBundle\Model\Theme as BaseTheme;

class Theme extends BaseTheme implements ThemeInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \SplFileInfo
     */
    protected $logo;

    /**
     * @var string
     */
    protected $logoPath;

    /**
     * @var array
     */
    protected $generatedData = [
        'routes' => [],
        'menus' => [],
        'contentLists' => [],
    ];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Theme constructor.
     *
     * @param string $name
     * @param string $path
     */
    public function __construct($name, $path)
    {
        if ($tempName = strstr($name, ThemeHelper::SUFFIX_SEPARATOR, true)) {
            $name = $tempName;
        }
        parent::__construct($name, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplates(): array
    {
        return $this->config['defaultTemplates'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(): array
    {
        return $this->generatedData['routes'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMenus(): array
    {
        return $this->generatedData['menus'];
    }

    /**
     * {@inheritdoc}
     */
    public function getContentLists(): array
    {
        return $this->generatedData['contentLists'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo(): ?\SplFileInfo
    {
        return $this->logo;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo(?\SplFileInfo $file): void
    {
        $this->logo = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogoPath(?string $path): void
    {
        $this->logoPath = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogo(): bool
    {
        return null !== $this->logo;
    }

    public function setGeneratedData(array $generatedData): void
    {
        $this->generatedData = $generatedData;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([
            $this->config,
            $this->generatedData,
            $this->settings,
            $this->name,
            $this->description,
            $this->path,
            $this->title,
            $this->logoPath,
            $this->parents,
            $this->authors,
            $this->screenshots,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [
            $this->config,
            $this->generatedData,
            $this->settings,
            $this->name,
            $this->description,
            $this->path,
            $this->title,
            $this->logoPath,
            $this->parents,
            $this->authors,
            $this->screenshots
        ] = unserialize($serialized);
    }
}
