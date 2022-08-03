<?php

/*
 * This file is part of the Superdesk Web Publisher <change_me> Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\Rule\Evaluator;

use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluator;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @mixin RuleEvaluator
 */
final class RuleEvaluatorSpec extends ObjectBehavior
{
    public function let(LoggerInterface $logger, ExpressionLanguage $expression)
    {
        $this->beConstructedWith($logger, $expression);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RuleEvaluator::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleEvaluatorInterface::class);
    }

    public function it_evaluates_the_rule_based_on_subject_to_false(
        RuleInterface $rule,
        RuleSubjectInterface $subject,
        ExpressionLanguage $expressionLanguage
    ) {
        $rule->getExpression()->shouldBeCalled();
        $subject->getSubjectType()->shouldBeCalled();

        $expressionLanguage->evaluate(
            'some_expression',
            ['some_type' => Argument::type(RuleSubjectInterface::class)]
        )->willReturn(false);

        $this->evaluate($rule, $subject)->shouldReturn(false);
    }

    public function it_evaluates_the_rule_based_on_subject_to_true(
        RuleInterface $rule,
        RuleSubjectInterface $subject,
        ExpressionLanguage $expression
    ) {
        $rule->getExpression()->shouldBeCalled()->willReturn('some_type.getSomething() == "something"');
        $subject->getSubjectType()->shouldBeCalled()->willReturn('some_type');

        $expression->evaluate(
            'some_type.getSomething() == "something"',
            ['some_type' => $subject]
        )->willReturn(true);

        $this->evaluate($rule, $subject)->shouldReturn(true);
    }

    public function it_evaluates_the_rule_based_on_subject_to_false_when_exception_is_thrown(
        RuleInterface $rule,
        RuleSubjectInterface $subject,
        LoggerInterface $logger,
        ExpressionLanguage $expression
    ) {
        $rule->getExpression()->willReturn('')->shouldBeCalled();
        $subject->getSubjectType()->shouldBeCalled();
        $expression->evaluate(
            Argument::type('string'),
            Argument::type('array')
        )->willThrow(\Exception::class);

        $logger->error(Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $this->evaluate($rule, $subject)->shouldReturn(false);
    }
}
