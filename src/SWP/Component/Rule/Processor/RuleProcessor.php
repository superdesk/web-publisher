<?php

namespace SWP\Component\Rule\Processor;

use SWP\Bundle\ContentBundle\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;

final class RuleProcessor implements RuleProcessorInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var RuleEvaluatorInterface
     */
    protected $ruleEvaluator;

    /**
     * @var RuleApplicatorInterface
     */
    protected $ruleApplicator;

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
            if ($this->ruleEvaluator->evaluate($rule, [$subject->getSubjectType() => $subject])) {
                $this->ruleApplicator->apply($rule, $subject);
            }
        }
    }
}
