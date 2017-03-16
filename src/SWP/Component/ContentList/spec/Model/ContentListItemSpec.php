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

namespace spec\SWP\Component\ContentList\Model;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItem;
use PhpSpec\ObjectBehavior;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Model\ListContentInterface;

/**
 * @mixin ContentListItem
 */
final class ContentListItemSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListItem::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(ContentListItemInterface::class);
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_position_by_default()
    {
        $this->getPosition()->shouldReturn(0);
    }

    public function its_position_is_mutable()
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }

    public function its_content_is_mutable(ListContentInterface $content)
    {
        $this->setContent($content);
        $this->getContent()->shouldReturn($content);
    }

    public function its_content_list_is_mutable(ContentListInterface $contentList)
    {
        $this->setContentList($contentList);
        $this->getContentList()->shouldReturn($contentList);
    }

    public function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    public function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    public function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
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

    public function it_should_return_true_if_it_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();
    }

    public function it_should_return_false_if_it_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    public function it_has_no_deleted_at_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    public function it_is_not_sticked_by_default()
    {
        $this->shouldNotBeSticky();
    }

    public function it_can_be_sticky()
    {
        $this->setSticky(true);
        $this->shouldBeSticky();
    }
}
