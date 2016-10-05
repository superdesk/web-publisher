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

namespace spec\SWP\Component\Rule\Processor;

use Prophecy\Argument;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Processor\RuleProcessor;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Processor\RuleProcessorInterface;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;

/**
 * @mixin RuleProcessor
 */
final class RuleProcessorSpec extends ObjectBehavior
{
    public function let(
        RuleRepositoryInterface $ruleRepository,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $this->beConstructedWith($ruleRepository, $ruleEvaluator, $ruleApplicator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RuleProcessor::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleProcessorInterface::class);
    }

    public function it_processes_rule_subject(
        RuleSubjectInterface $subject,
        RuleRepositoryInterface $ruleRepository,
        RuleInterface $rule1,
        RuleInterface $rule2,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $ruleRepository->findBy([], ['priority' => 'desc'])->willReturn([$rule1, $rule2]);
        $ruleEvaluator->evaluate(Argument::type(RuleInterface::class), $subject)->willReturn(true);
        $ruleApplicator->apply(Argument::type(RuleInterface::class), $subject)->shouldBeCalled();

        $this->process($subject);
    }

    public function it_is_not_processing_rule_subject(
        RuleSubjectInterface $subject,
        RuleRepositoryInterface $ruleRepository,
        RuleInterface $rule1,
        RuleInterface $rule2,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $ruleRepository->findBy([], ['priority' => 'desc'])->willReturn([$rule1, $rule2]);
        $ruleEvaluator->evaluate(Argument::type(RuleInterface::class), $subject)->willReturn(false);
        $ruleApplicator->apply(Argument::type(RuleInterface::class), $subject)->shouldNotBeCalled();

        $this->process($subject);
    }

    public function it_procesess_nothing(
        RuleSubjectInterface $subject,
        RuleRepositoryInterface $ruleRepository,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $ruleRepository->findBy([], ['priority' => 'desc'])->willReturn([]);
        $ruleEvaluator->evaluate(Argument::type(RuleInterface::class), $subject)->shouldNotBeCalled();
        $ruleApplicator->apply(Argument::type(RuleInterface::class), $subject)->shouldNotBeCalled();

        $this->process($subject);
    }
}
