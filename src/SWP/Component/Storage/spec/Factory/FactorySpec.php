<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\Storage\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Component\Storage\Factory\Factory;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin Factory
 */
class FactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(\stdClass::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Factory::class);
    }

    public function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    public function it_creates_an_instance_of_new_object()
    {
        $this->create()->shouldBeAnInstanceOf(\stdClass::class);
    }
}
