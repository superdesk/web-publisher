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

namespace SWP\Bundle\CoreBundle\Rule\Processor;

use SWP\Bundle\CoreBundle\Repository\RuleRepositoryInterface;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Processor\RuleProcessorInterface;

final class OrganizationRuleProcessor implements RuleProcessorInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleEvaluatorInterface
     */
    private $ruleEvaluator;

    /**
     * @var RuleApplicatorInterface
     */
    private $ruleApplicator;

    /**
     * RuleProcessor constructor.
     *
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleEvaluatorInterface  $ruleEvaluator
     * @param RuleApplicatorInterface $ruleApplicator
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        RuleEvaluatorInterface $ruleEvaluator,
        RuleApplicatorInterface $ruleApplicator
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleEvaluator = $ruleEvaluator;
        $this->ruleApplicator = $ruleApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(RuleSubjectInterface $subject)
    {
        $rules = $this->ruleRepository->findOrganizationRules();

        foreach ($rules as $rule) {
            if ($this->ruleEvaluator->evaluate($rule, $subject)) {
                $this->ruleApplicator->apply($rule, $subject);
            }
        }
    }
}
