<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\CoreBundle\Document;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Document\Organization;
use SWP\Component\MultiTenancy\Model\Organization as BaseOranization;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;

/**
 * @mixin Organization
 */
class OrganizationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Organization::class);
        $this->shouldHaveType(BaseOranization::class);
    }

    public function it_implements_organization_interface()
    {
        $this->shouldImplement(OrganizationInterface::class);
    }

    public function it_has_no_parent_by_default()
    {
        $this->getParentDocument()->shouldReturn(null);
        $this->getParent()->shouldReturn(null);
    }

    public function its_parent_is_mutable(\stdClass $object)
    {
        $this->setParentDocument($object);
        $this->getParentDocument()->shouldReturn($object);
        $this->getParent()->shouldReturn($object);
    }
}
