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

namespace spec\SWP\Bundle\CoreBundle\Rule\Processor;

use SWP\Bundle\CoreBundle\Model\RuleInterface;
use SWP\Bundle\CoreBundle\Repository\RuleRepositoryInterface;
use SWP\Bundle\CoreBundle\Rule\Processor\OrganizationRuleProcessor;
use PhpSpec\ObjectBehavior;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Processor\RuleProcessorInterface;

final class OrganizationRuleProcessorSpec extends ObjectBehavior
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
        $this->shouldHaveType(OrganizationRuleProcessor::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(RuleProcessorInterface::class);
    }

    public function it_processes(
        RuleRepositoryInterface $ruleRepository,
        RuleInterface $rule,
        RuleSubjectInterface $ruleSubject,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $rule->getExpression()->willReturn('expression');
        $rule->getTenantCode()->willReturn(null);

        $rules = [$rule];
        $ruleSubject->getSubjectType()->willReturn('package');

        $ruleRepository->findOrganizationRules()->willReturn($rules);
        $ruleEvaluator->evaluate($rule, $ruleSubject)->willReturn(true);

        $ruleApplicator->apply($rule, $ruleSubject)->shouldBeCalled();

        $this->process($ruleSubject);
    }
}
