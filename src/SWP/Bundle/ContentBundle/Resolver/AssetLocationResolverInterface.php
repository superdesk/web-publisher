<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Resolver;

use SWP\Bundle\ContentBundle\Model\FileInterface;

interface AssetLocationResolverInterface
{
    public const ASSET_BASE_PATH = 'swp/media';

    public function getAssetUrl(FileInterface $file): string;

    public function getMediaBasePath(): string;
}
