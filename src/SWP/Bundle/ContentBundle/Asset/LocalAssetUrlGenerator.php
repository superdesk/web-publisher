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

namespace SWP\Bundle\ContentBundle\Asset;

use SWP\Bundle\ContentBundle\Model\FileInterface;

final class LocalAssetUrlGenerator implements AssetUrlGeneratorInterface
{
    private $localDirectory;

    public function __construct(string $localDirectory = null)
    {
        $this->localDirectory = $localDirectory;
    }

    public function generateUrl(FileInterface $file, string $basePath): string
    {
        return ($this->localDirectory ? $this->localDirectory.DIRECTORY_SEPARATOR : null).
            $basePath.
            DIRECTORY_SEPARATOR.
            $file->getAssetId().
            '.'.
            $file->getFileExtension()
        ;
    }
}
