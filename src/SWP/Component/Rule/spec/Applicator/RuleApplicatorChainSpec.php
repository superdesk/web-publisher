<?php

/*
 * This file is part of the Superdesk Web Publisher Rule Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Rule\Applicator;

use SWP\Component\Rule\Applicator\RuleApplicatorChain;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;

/**
 * @mixin RuleApplicatorChain
 */
final class RuleApplicatorChainSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RuleApplicatorChain::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleApplicatorInterface::class);
    }

    public function it_adds_a_new_applicator(RuleApplicatorInterface $applicator)
    {
        $this->addApplicator($applicator);
    }

    public function it_is_supportable(
        RuleSubjectInterface $subject,
        RuleApplicatorInterface $applicator
    ) {
        $applicator->isSupported($subject)->willReturn(true);
        $this->addApplicator($applicator);

        $subject->getSubjectType()->willReturn('article');

        $this->isSupported($subject)->shouldReturn(true);
    }

    public function it_is_not_supportable(
        RuleSubjectInterface $subject,
        RuleApplicatorInterface $applicator
    ) {
        $applicator->isSupported($subject)->willReturn(false);
        $this->addApplicator($applicator);

        $subject->getSubjectType()->willReturn('article');

        $this->isSupported($subject)->shouldReturn(false);
    }
}
