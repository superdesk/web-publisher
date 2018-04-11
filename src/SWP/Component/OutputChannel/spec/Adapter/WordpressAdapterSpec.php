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

namespace spec\SWP\Component\OutputChannel\Adapter;

use SWP\Component\OutputChannel\Adapter\AdapterInterface;
use SWP\Component\OutputChannel\Adapter\WordpressAdapter;
use PhpSpec\ObjectBehavior;

final class WordpressAdapterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WordpressAdapter::class);
    }

    function it_implements_interface()
    {
        $this->shouldImplement(AdapterInterface::class);
    }
}
