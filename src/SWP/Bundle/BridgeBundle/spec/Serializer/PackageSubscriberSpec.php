<?php

/*
 * This file is part of the Superdesk Web Publisher <change_me> Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\BridgeBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Bundle\BridgeBundle\Serializer\PackageSubscriber;
use PhpSpec\ObjectBehavior;

/**
 * @mixin PackageSubscriber
 */
final class PackageSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PackageSubscriber::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function it_subscribes_to_event()
    {
        self::getSubscribedEvents()->shouldReturn([
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
            ],
        ]);
    }

    public function it_does_nothing_on_post_deserialize(ObjectEvent $event)
    {
        $event->getObject()->willReturn(new \stdClass());

        $this->onPostDeserialize($event);
    }
}
