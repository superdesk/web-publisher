<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * Interface ContainerRepository.
 */
interface ContainerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return QueryBuilder
     */
    public function getByName(string $name): QueryBuilder;

    /**
     * Get Query for Container searched by id.
     *
     * @param string $id
     *
     * @return QueryBuilder
     */
    public function getById($id): QueryBuilder;

    /**
     * Get Query for Container searched by id but only with id, createdAt and updatedAt fields.
     *
     * @param string $id
     *
     * @return \Doctrine\ORM\Query
     */
    public function getHttpCacheCheckQuery($id): Query;

    /**
     * Get Query for all Containers.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAll(): Query;
}
