<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Resolver;

use SWP\Bundle\ContentBundle\Asset\AssetUrlGeneratorInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;

class AssetLocationResolver implements AssetLocationResolverInterface
{
    private $assetUrlGenerator;

    public function __construct(AssetUrlGeneratorInterface $assetUrlGenerator)
    {
        $this->assetUrlGenerator = $assetUrlGenerator;
    }

    public function getAssetUrl(FileInterface $file): string
    {
        $basePath = $this->getMediaBasePath();

        return $this->assetUrlGenerator->generateUrl($file, $basePath);
    }

    public function getMediaBasePath(): string
    {
        return AssetLocationResolverInterface::ASSET_BASE_PATH;
    }
}
