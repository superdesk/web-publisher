<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Organization extends AbstractOrganization implements OrganizationInterface
{
    /**
     * @var Collection|TenantInterface[]
     */
    protected $tenants;

    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->tenants = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getTenants()
    {
        return $this->tenants;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTenant(TenantInterface $tenant)
    {
        return $this->tenants->contains($tenant);
    }

    /**
     * {@inheritdoc}
     */
    public function addTenant(TenantInterface $tenant)
    {
        if (!$this->hasTenant($tenant)) {
            $this->tenants->add($tenant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTenant(TenantInterface $tenant)
    {
        if ($this->hasTenant($tenant)) {
            $this->tenants->removeElement($tenant);
        }
    }
}
