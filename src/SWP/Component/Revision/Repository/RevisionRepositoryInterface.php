<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Revision\Repository;

use Doctrine\ORM\QueryBuilder;

interface RevisionRepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function getPublishedRevision(): QueryBuilder;

    /**
     * @return QueryBuilder
     */
    public function getWorkingRevision(): QueryBuilder;

    /**
     * @param string $key
     *
     * @return QueryBuilder
     */
    public function getByKey($key): QueryBuilder;
}
