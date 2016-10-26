<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Factory;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

interface ContentListFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     *
     * @return ContentListInterface
     */
    public function createTyped(string $type): ContentListInterface;
}
