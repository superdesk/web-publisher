<?php

/*
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
namespace spec\SWP\Bundle\CoreBundle\Entity;

use SWP\Bundle\CoreBundle\Entity\Rule;
use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * @mixin Rule
 */
final class RuleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Rule::class);
        $this->shouldHaveType(RuleInterface::class);
    }

    function it_implements_an_interface()
    {
        $this->shouldImplement(TenantAwareInterface::class);
        $this->shouldImplement(PersistableInterface::class);
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
}
