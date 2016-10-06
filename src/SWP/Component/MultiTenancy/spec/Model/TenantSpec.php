<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\MultiTenancy\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\Tenant;

/**
 * @mixin Tenant
 */
class TenantSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\Model\Tenant');
    }

    public function it_implements_tenant_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\Model\TenantInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function it_has_no_subdomain_by_default()
    {
        $this->getSubdomain()->shouldReturn(null);
    }

    public function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    public function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    public function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    public function it_doesnt_have_fluent_interface()
    {
        $this->setId(1)->shouldNotReturn($this);
        $this->setName('Tenant 1')->shouldNotReturn($this);
        $this->setSubdomain('client1')->shouldNotReturn($this);
    }

    public function it_should_return_true_if_tenant_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();
    }

    public function it_should_return_false_if_tenant_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    public function it_has_no_deleted_at_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    public function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    public function it_should_not_allow_to_change_code_once_its_set()
    {
        $this->setCode('code');

        $this
            ->shouldThrow('LogicException')
            ->during('setCode', ['newcode']);
    }
}
