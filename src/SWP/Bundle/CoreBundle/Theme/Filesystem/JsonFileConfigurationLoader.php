<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Filesystem;

use InvalidArgumentException;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;

final class JsonFileConfigurationLoader implements ConfigurationLoaderInterface
{
    private $themeAssetProvider;

    public function __construct(ThemeAssetProviderInterface $themeAssetProvider)
    {
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $identifier): array
    {
        $this->assertFileExists($identifier);

        $contents = $this->themeAssetProvider->readFile($identifier);

        return array_merge(
            ['path' => dirname($identifier)],
            json_decode($contents, true)
        );
    }

    private function assertFileExists(string $path): void
    {
        if (!$this->themeAssetProvider->hasFile($path)) {
            throw new InvalidArgumentException(sprintf(
                'Given file "%s" does not exist!',
                $path
            ));
        }
    }
}
