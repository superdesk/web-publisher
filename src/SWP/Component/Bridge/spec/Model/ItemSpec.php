<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\Bridge\Model;

use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Model\BaseContent;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Common\Model\TimestampableInterface;

/**
 * @mixin Item
 */
class ItemSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Item::class);
        $this->shouldHaveType(BaseContent::class);
    }

    public function it_should_implement_interfaces()
    {
        $this->shouldImplement(ItemInterface::class);
        $this->shouldImplement(TimestampableInterface::class);
    }

    public function it_has_no_body_by_default()
    {
        $this->getBody()->shouldReturn(null);
    }

    public function its_slug_is_mutable()
    {
        $this->setBody('body');
        $this->getBody()->shouldReturn('body');
    }

    public function it_has_no_date_by_default()
    {
        $this->getCreatedAt()->shouldReturn(null);
    }

    public function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
