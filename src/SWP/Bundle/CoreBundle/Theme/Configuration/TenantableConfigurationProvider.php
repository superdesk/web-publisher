<?php

/**
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

namespace SWP\Bundle\CoreBundle\Theme\Configuration;

use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelperInterface;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

class TenantableConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var ConfigurationLoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $configurationFilename;

    /**
     * @var ThemeHelperInterface
     */
    private $themeHelper;

    /**
     * @param FileLocatorInterface         $fileLocator
     * @param ConfigurationLoaderInterface $loader
     * @param string                       $configurationFilename
     * @param ThemeHelperInterface         $themeHelper
     */
    public function __construct(
        FileLocatorInterface $fileLocator,
        ConfigurationLoaderInterface $loader,
        $configurationFilename,
        ThemeHelperInterface $themeHelper
    ) {
        $this->fileLocator = $fileLocator;
        $this->loader = $loader;
        $this->configurationFilename = $configurationFilename;
        $this->themeHelper = $themeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurations()
    {
        // Handle there being no theme.json file
        try {
            $configs = array_map(
                [$this->loader, 'load'],
                $this->fileLocator->locateFilesNamed($this->configurationFilename)
            );

            return array_map(
                [$this->themeHelper, 'process'],
                $configs
            );
        } catch (\InvalidArgumentException $e) {
            return [];
        }
    }
}
