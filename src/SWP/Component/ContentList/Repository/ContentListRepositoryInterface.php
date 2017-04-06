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

namespace SWP\Component\ContentList\Repository;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface ContentListRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $types
     *
     * @return array
     */
    public function findByTypes(array $types): array;

    /**
     * @param int $listId
     *
     * @return null|ContentListInterface
     */
    public function findListById(int $listId);
}
