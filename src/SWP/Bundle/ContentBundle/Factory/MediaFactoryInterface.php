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

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\Bridge\Model\ItemInterface;

interface MediaFactoryInterface
{
    public function create(ArticleInterface $article, string $key, ItemInterface $item, string $type = ArticleMediaInterface::TYPE_EMBEDDED_IMAGE): ArticleMediaInterface;

    public function createEmpty(): ArticleMediaInterface;
}
