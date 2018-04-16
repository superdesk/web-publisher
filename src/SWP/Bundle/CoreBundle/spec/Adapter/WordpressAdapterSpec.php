<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Adapter;

use GuzzleHttp\ClientInterface;
use SWP\Bundle\CoreBundle\Adapter\AdapterInterface;
use SWP\Bundle\CoreBundle\Adapter\WordpressAdapter;
use PhpSpec\ObjectBehavior;

final class WordpressAdapterSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WordpressAdapter::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement(AdapterInterface::class);
    }
}
