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

namespace SWP\Bundle\CoreBundle\Matcher;

use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Processor\RulesProcessorInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RulesMatcher implements RulesMatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleEvaluatorInterface
     */
    private $ruleEvaluator;

    /**
     * @var RulesProcessorInterface
     */
    private $rulesProcessor;

    /**
     * @var ArticleFactoryInterface
     */
    private $articleFactory;

    /**
     * RulesMatcher constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param RuleRepositoryInterface  $ruleRepository
     * @param RuleEvaluatorInterface   $ruleEvaluator
     * @param RulesProcessorInterface  $rulesProcessor
     * @param ArticleFactoryInterface  $articleFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RuleRepositoryInterface $ruleRepository,
        RuleEvaluatorInterface $ruleEvaluator,
        RulesProcessorInterface $rulesProcessor,
        ArticleFactoryInterface $articleFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->ruleRepository = $ruleRepository;
        $this->ruleEvaluator = $ruleEvaluator;
        $this->rulesProcessor = $rulesProcessor;
        $this->articleFactory = $articleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchedRules(PackageInterface $package): array
    {
        $article = $this->articleFactory->createFromPackage($package);
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $rules = $this->ruleRepository->findBy([], ['priority' => 'desc']);

        $evaluatedRules = [];
        foreach ($rules as $rule) {
            if ($this->ruleEvaluator->evaluate($rule, $package)) {
                $evaluatedRules[] = $rule;
            }

            if ($this->ruleEvaluator->evaluate($rule, $article)) {
                $evaluatedRules[] = $rule;
            }
        }

        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);

        return $this->rulesProcessor->process($evaluatedRules);
    }
}
