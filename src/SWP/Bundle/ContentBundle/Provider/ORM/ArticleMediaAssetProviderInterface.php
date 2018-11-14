<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Bridge\Model\RenditionInterface;

interface ArticleMediaAssetProviderInterface
{
    public function getImage(RenditionInterface $rendition): ?ImageInterface;

    public function getFile(RenditionInterface $rendition): ?FileInterface;
}
