<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine;

use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface ImageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $assetId
     *
     * @return null|ImageInterface
     */
    public function findImageByAssetId(string $assetId);
}
