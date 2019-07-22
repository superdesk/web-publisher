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

namespace SWP\Bundle\CoreBundle\Locator;

use SWP\Bundle\CoreBundle\Detection\DeviceDetectionInterface;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

class ApplicationResourceLocator implements ResourceLocatorInterface
{
    private $themeAssetProvider;

    private $deviceDetection;

    public function __construct(ThemeAssetProviderInterface $themeAssetProvider, DeviceDetectionInterface $deviceDetection)
    {
        $this->themeAssetProvider = $themeAssetProvider;
        $this->deviceDetection = $deviceDetection;
    }

    public function locateResource(string $resourceName, ThemeInterface $theme): string
    {
        if (null !== $this->deviceDetection->getType()) {
            $path = sprintf('%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $resourceName);
            if ($this->themeAssetProvider->hasFile($path)) {
                return $path;
            }
        }

        $path = sprintf('%s/%s', $theme->getPath(), $resourceName);
        if (!$this->themeAssetProvider->hasFile($path)) {
            throw new ResourceNotFoundException($resourceName, $theme);
        }

        return $path;
    }
}
