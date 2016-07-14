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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use SWP\Component\Bridge\Model\BaseContent;
use SWP\Component\Bridge\Model\Package;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * @mixin Package
 */
class PackageSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Package::class);
        $this->shouldHaveType(BaseContent::class);
    }

    public function it_should_implement_interfaces()
    {
        $this->shouldImplement(PackageInterface::class);
        $this->shouldImplement(PersistableInterface::class);
    }

    public function it_has_no_items_by_default()
    {
        $this->getItems()->shouldHaveType(Collection::class);
    }

    public function it_has_no_creation_date_by_default()
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

    public function it_should_return_true_if_article_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        $this->shouldBeDeleted();
    }

    public function it_should_return_false_if_article_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    public function it_has_no_deleted_at_date_by_default()
    {
        $this->getDeletedAt()->shouldReturn(null);
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
}
