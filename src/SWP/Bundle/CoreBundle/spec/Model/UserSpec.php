<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\UserBundle\Model\UserInterface as BaseUserInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\User;
use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;

final class UserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(User::class);
    }

    public function it_implements_interface()
    {
        $this->shouldHaveType(UserInterface::class);
    }

    public function it_extends_user()
    {
        $this->shouldHaveType(BaseUserInterface::class);
    }

    public function it_has_no_organization_by_default()
    {
        $this->getOrganization()->shouldReturn(null);
    }

    public function its_organization_is_mutable(OrganizationInterface $organization)
    {
        $this->setOrganization($organization);
        $this->getOrganization()->shouldReturn($organization);
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
}
