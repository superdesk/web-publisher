<?php

declare(strict_types=1);

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

use SWP\Bundle\ContentBundle\Model\FileInterface;

final class LocalAssetUrlResolver implements AssetLocationResolverInterface
{
    private $localDirectory;

    public function __construct(string $localDirectory = null)
    {
        $this->localDirectory = $localDirectory;
    }

    public function getAssetUrl(FileInterface $file): string
    {
        return ($this->localDirectory ? $this->localDirectory.DIRECTORY_SEPARATOR : null).
            AssetLocationResolverInterface::ASSET_BASE_PATH.
            DIRECTORY_SEPARATOR.
            $file->getAssetId().
            '.'.
            $file->getFileExtension()
        ;
    }

    public function getMediaBasePath(): string
    {
        return AssetLocationResolverInterface::ASSET_BASE_PATH;
    }
}
