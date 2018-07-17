<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Paywall Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Paywall\Factory;

use SWP\Component\Paywall\Factory\SubscriptionFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Component\Paywall\Factory\SubscriptionFactoryInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

final class SubscriptionFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionFactory::class);
    }

    function it_implements_subscription_factory_interface(): void
    {
        $this->shouldImplement(SubscriptionFactoryInterface::class);
    }

    function it_creates_empty_subscription(): void
    {
        $this->create()->shouldBeAnInstanceOf(SubscriptionInterface::class);
    }
}
