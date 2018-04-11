<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\OutputChannel\Provider;

use SWP\Component\OutputChannel\Adapter\AdapterInterface;
use SWP\Component\OutputChannel\Model\OutputChannelInterface;
use SWP\Component\OutputChannel\Provider\AdapterProviderInterface;
use SWP\Component\OutputChannel\Provider\WordpressAdapterProvider;
use PhpSpec\ObjectBehavior;

final class WordpressAdapterProviderSpec extends ObjectBehavior
{
    function let(AdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WordpressAdapterProvider::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement(AdapterProviderInterface::class);
    }

    function it_supports(OutputChannelInterface $outputChannel)
    {
        $outputChannel->getType()->willReturn('wordpress');

        $this->supports($outputChannel)->shouldReturn(true);
    }

    function it_does_not_support_other_types(OutputChannelInterface $outputChannel)
    {
        $outputChannel->getType()->willReturn('fake');

        $this->supports($outputChannel)->shouldReturn(false);
    }

    function it_gets_adapter(OutputChannelInterface $outputChannel, AdapterInterface $adapter)
    {
        $outputChannel->getType()->willReturn('wordpress');
        $outputChannel->getConfig()->willReturn(['key' => 'value']);

        $this->get($outputChannel)->shouldReturn($adapter);
    }
}
