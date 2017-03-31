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

namespace spec\SWP\Bundle\CoreBundle\Factory;

use DeepCopy\DeepCopy;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Factory\ClonerFactory;
use SWP\Bundle\CoreBundle\Factory\ClonerFactoryInterface;

final class ClonerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ClonerFactory::class);
    }

    public function it_implements_cloner_factory_interface()
    {
        $this->shouldImplement(ClonerFactoryInterface::class);
    }

    public function it_creates_a_new_cloner_object()
    {
        $this->create()->shouldHaveType(DeepCopy::class);
    }
}
