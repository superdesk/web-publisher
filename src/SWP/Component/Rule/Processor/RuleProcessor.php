<?php

/**
 * This file is part of the Superdesk Web Publisher Rule Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Rule\Processor;

use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;

final class RuleProcessor implements RuleProcessorInterface
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

    public function process(RuleSubjectInterface $subject)
    {
        $rules = $this->ruleRepository->findBy([], ['priority' => 'desc']);

        foreach ($rules as $rule) {
            if (!$this->ruleEvaluator->evaluate($rule, $subject)) {
                continue;
            }

            $this->ruleApplicator->apply($rule, $subject);
        }
    }
}
