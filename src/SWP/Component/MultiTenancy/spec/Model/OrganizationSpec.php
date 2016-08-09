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
namespace spec\SWP\Component\MultiTenancy\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\Organization;

/**
 * @mixin Organization
 */
class OrganizationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\Model\Organization');
    }

    function it_implements_tenant_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\Model\OrganizationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function it_should_not_allow_to_change_code_once_its_set()
    {
        $this->setCode('code');

        $this
            ->shouldThrow('LogicException')
            ->during('setCode', ['newcode']);
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_doesnt_have_fluent_interface()
    {
        $this->setId(1)->shouldNotReturn($this);
        $this->setName('Tenant 1')->shouldNotReturn($this);
        $this->setCode('code')->shouldNotReturn($this);
    }

    function it_should_return_true_if_organization_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();
    }

    function it_should_return_false_if_tenant_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    function it_has_no_deleted_at_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }
}
