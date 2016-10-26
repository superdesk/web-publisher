<?php

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\ContentList\Factory;

use SWP\Component\ContentList\Factory\ContentListFactory;
use PhpSpec\ObjectBehavior;
use SWP\Component\ContentList\Factory\ContentListFactoryInterface;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin ContentListFactory
 */
final class ContentListFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListFactory::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(ContentListFactoryInterface::class);
    }

    public function it_creates_a_new_content_list(
        FactoryInterface $factory,
        ContentListInterface $contentList
    ) {
        $factory->create()->willReturn($contentList);

        $this->create()->shouldReturn($contentList);
    }

    public function it_creates_a_new_content_list_with_type(
        FactoryInterface $factory,
        ContentListInterface $contentList
    ) {
        $factory->create()->willReturn($contentList);
        $contentList->setType('test')->shouldBeCalled();

        $this->createTyped('test')->shouldReturn($contentList);
    }
}
