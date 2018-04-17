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

namespace spec\SWP\Component\OutputChannel\Model;

use SWP\Component\OutputChannel\Model\OutputChannel;
use PhpSpec\ObjectBehavior;
use SWP\Component\OutputChannel\Model\OutputChannelInterface;

final class OutputChannelSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OutputChannel::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement(OutputChannelInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn('');
    }

    function its_type_is_mutable()
    {
        $this->setType('wordpress');
        $this->getType()->shouldReturn('wordpress');
    }

    function it_has_no_config_by_default()
    {
        $this->getConfig()->shouldReturn([]);
    }

    function its_config_is_mutable()
    {
        $this->setConfig(['key' => 'value']);
        $this->getConfig()->shouldReturn(['key' => 'value']);
    }
}
