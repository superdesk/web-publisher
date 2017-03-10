<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Repository;

use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * Repository interface for organizations.
 */
interface OrganizationRepositoryInterface extends RepositoryInterface
{
    /**
     * Finds the organization by name.
     *
     * @param string $name The name
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return OrganizationInterface|null The instance of OrganizationInterface or null
     */
    public function findOneByName($name);

    /**
     * Finds the organization by code.
     *
     * @param string $code The code
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return OrganizationInterface|null The instance of OrganizationInterface or null
     */
    public function findOneByCode($code);

    /**
     * Finds all available organizations.
     *
     * @return array An array of organizations
     */
    public function findAvailable();
}
