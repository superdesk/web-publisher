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
    public function it_is_initializable()
    {
        $this->shouldHaveType(Rule::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleInterface::class);
        $this->shouldImplement(PersistableInterface::class);
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_expression_by_default()
    {
        $this->getExpression()->shouldReturn(null);
    }

    public function its_expression_is_mutable()
    {
        $this->setExpression('some expression');
        $this->getExpression()->shouldReturn('some expression');
    }

    public function it_has_no_priority_by_default()
    {
        $this->getPriority()->shouldReturn(null);
    }

    public function its_priority_is_mutable()
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    public function it_has_no_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    public function its_configuration_is_mutable()
    {
        $this->setConfiguration(['key' => 'value']);
        $this->getConfiguration()->shouldReturn(['key' => 'value']);
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription('description');
        $this->getDescription()->shouldReturn('description');
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('name');
        $this->getName()->shouldReturn('name');
    }
}
