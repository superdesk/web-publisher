<?php

declare(strict_types=1);

namespace spec\SWP\Component\Plan\Model;

use SWP\Component\Plan\Model\Plan;
use PhpSpec\ObjectBehavior;
use SWP\Component\Plan\Model\PlanInterface;

final class PlanSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Plan::class);
    }

    function it_implements_plan_interface(): void
    {
        $this->shouldImplement(PlanInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('pro-plan');
        $this->getCode()->shouldReturn('pro-plan');
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Pro Plan');
        $this->getName()->shouldReturn('Pro Plan');
    }

    function it_has_amount_equal_to_0_by_default(): void
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable(): void
    {
        $this->setAmount(5000);
        $this->getAmount()->shouldReturn(5000);
    }

    function it_has_default_interval_value(): void
    {
        $this->getInterval()->shouldReturn(PlanInterface::INTERVAL_MONTH);
    }

    function its_interval_is_mutable(): void
    {
        $this->setInterval('day');
        $this->getInterval()->shouldReturn('day');
    }

    function it_has_default_interval_count_value(): void
    {
        $this->getIntervalCount()->shouldReturn(1);
    }

    function its_interval_count_is_mutable(): void
    {
        $this->setIntervalCount(2);
        $this->getIntervalCount()->shouldReturn(2);
    }

    function it_has_no_currency_by_default(): void
    {
        $this->getCurrency()->shouldReturn(null);
    }

    function its_currency_is_mutable(): void
    {
        $this->setCurrency('USD');
        $this->getCurrency()->shouldReturn('USD');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_creation_date_is_mutable(): void
    {
        $date = new \DateTime('now');
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_updated_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_updated_date_is_mutable(): void
    {
        $date = new \DateTime('now');
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_no_deleted_date_by_default(): void
    {
        $this->getDeletedAt()->shouldReturn(null);
    }

    function its_deleted_date_is_mutable(): void
    {
        $date = new \DateTime('now');
        $this->setDeletedAt($date);
        $this->getDeletedAt()->shouldReturn($date);
    }
}
