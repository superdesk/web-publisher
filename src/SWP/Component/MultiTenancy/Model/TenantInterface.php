<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\Model;

use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;

/**
 * Defines the interface of tenants.
 */
interface TenantInterface extends TimestampableInterface, EnableableInterface, SoftDeletableInterface
{
    /**
     * Gets the tenant identifier.
     *
     * @return mixed The tenant identifier
     */
    public function getId();

    /**
     * Sets the tenant identifier.
     *
     * @param mixed $id The tenant identifier
     */
    public function setId($id);

    /**
     * Gets the tenant subdomain.
     *
     * @return string The tenant subdomain
     */
    public function getSubdomain();

    /**
     * Sets the tenant identifier.
     *
     * @param string $subdomain The tenant subdomain
     */
    public function setSubdomain($subdomain);

    /**
     * Gets the tenant name.
     *
     * @return string The tenant name
     */
    public function getName();

    /**
     * Sets the tenant name.
     *
     * @param string $name The tenant name
     */
    public function setName($name);
}
