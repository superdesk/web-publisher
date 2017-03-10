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

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\User;
use SWP\Bundle\CoreBundle\Model\UserInterface;

final class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_interface()
    {
        $this->shouldHaveType(UserInterface::class);
    }

    function it_extends_user()
    {
        $this->shouldHaveType(BaseUserInterface::class);
    }

    function it_has_no_tenant_code_by_default()
    {
        $this->getTenantCode()->shouldReturn(null);
    }

    function its_tenant_code_is_mutable()
    {
        $this->setTenantCode('eyt645');
        $this->getTenantCode()->shouldReturn('eyt645');
    }

    public function it_should_initialize_creation_date_by_default()
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
}
