<?php

/*
 * This file is part of the Superdesk Web Publisher Rule Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\Rule\Model;

use SWP\Component\Rule\Model\Rule;
use PhpSpec\ObjectBehavior;
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
    }

    function it_implements_an_interface()
    {
        $this->shouldImplement(RuleInterface::class);
        $this->shouldImplement(PersistableInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_expression_by_default()
    {
        $this->getExpression()->shouldReturn(null);
    }

    function its_expression_is_mutable()
    {
        $this->setExpression('some expression');
        $this->getExpression()->shouldReturn('some expression');
    }

    function it_has_no_priority_by_default()
    {
        $this->getPriority()->shouldReturn(null);
    }

    function its_priority_is_mutable()
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    function it_has_no_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(['key' => 'value']);
        $this->getConfiguration()->shouldReturn(['key' => 'value']);
    }
}
