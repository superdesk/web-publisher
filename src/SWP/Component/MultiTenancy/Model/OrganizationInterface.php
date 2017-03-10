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

namespace SWP\Component\MultiTenancy\Model;

use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface OrganizationInterface extends PersistableInterface, SoftDeletableInterface, EnableableInterface, TimestampableInterface
{
    const DEFAULT_NAME = 'default';

    /**
     * Gets the organization identifier.
     *
     * @return mixed The organization identifier
     */
    public function getId();

    /**
     * Sets the organization identifier.
     *
     * @param mixed $id The organization identifier
     */
    public function setId($id);

    /**
     * Gets the organization name.
     *
     * @return string The organization name
     */
    public function getName();

    /**
     * Sets the organization name.
     *
     * @param string $name The organization name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return TenantInterface[]
     */
    public function getTenants();

    /**
     * @param TenantInterface $tenant
     *
     * @return bool
     */
    public function hasTenant(TenantInterface $tenant);

    /**
     * @param TenantInterface $tenant
     */
    public function addTenant(TenantInterface $tenant);

    /**
     * @param TenantInterface $tenant
     */
    public function removeTenant(TenantInterface $tenant);
}
