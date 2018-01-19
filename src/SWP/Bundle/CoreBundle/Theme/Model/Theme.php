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
     * @var array
     */
    protected $generatedData = [
        'routes' => [],
        'menus' => [],
        'containers' => [],
        'widgets' => [],
        'contentLists' => [],
    ];

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
    public function getContainers(): array
    {
        return $this->generatedData['containers'];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets(): array
    {
        return $this->generatedData['widgets'];
    }

    /**
     * {@inheritdoc}
     */
    public function getContentLists(): array
    {
        return $this->generatedData['contentLists'];
    }
}
