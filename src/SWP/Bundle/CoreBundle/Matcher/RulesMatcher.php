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
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Processor\RulesProcessorInterface;
use SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface;
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
     * @var PublishDestinationProviderInterface
     */
    private $publishDestinationProvider;

    /**
     * RulesMatcher constructor.
     *
     * @param EventDispatcherInterface            $eventDispatcher
     * @param RuleRepositoryInterface             $ruleRepository
     * @param RuleEvaluatorInterface              $ruleEvaluator
     * @param RulesProcessorInterface             $rulesProcessor
     * @param ArticleFactoryInterface             $articleFactory
     * @param PublishDestinationProviderInterface $publishDestinationProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RuleRepositoryInterface $ruleRepository,
        RuleEvaluatorInterface $ruleEvaluator,
        RulesProcessorInterface $rulesProcessor,
        ArticleFactoryInterface $articleFactory,
        PublishDestinationProviderInterface $publishDestinationProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->ruleRepository = $ruleRepository;
        $this->ruleEvaluator = $ruleEvaluator;
        $this->rulesProcessor = $rulesProcessor;
        $this->articleFactory = $articleFactory;
        $this->publishDestinationProvider = $publishDestinationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchedRules(PackageInterface $package): array
    {
        $article = $this->articleFactory->createFromPackage($package);
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $destinations = $this->publishDestinationProvider->getDestinations($package);

        $evaluatedOrganizationRules = $this->processPackageRules($package);
        $evaluatedRules = $this->processArticleRules($article, $package, $destinations);
        $processedRules = $this->rulesProcessor->process(array_merge($evaluatedOrganizationRules, $evaluatedRules));

        foreach ($destinations as $destination) {
            foreach ((array) $processedRules['tenants'] as $key => $tenant) {
                if ($tenant['tenant'] === $destination->getTenant()) {
                    $processedRules['tenants'][$key] = [
                        'tenant' => $destination->getTenant(),
                        'routes' => [
                            $destination->getRoute(),
                        ],
                    ];
                } else {
                    $processedRules['tenants'][$key] = $tenant;
                }
            }
        }

        return $processedRules;
    }

    private function processPackageRules(PackageInterface $package): array
    {
        $organizationRules = $this->ruleRepository->findBy(['tenantCode' => null], ['priority' => 'desc']);

        $evaluatedOrganizationRules = [];
        foreach ($organizationRules as $rule) {
            if ($this->ruleEvaluator->evaluate($rule, $package)) {
                $evaluatedOrganizationRules[] = $rule;
            }
        }

        return $evaluatedOrganizationRules;
    }

    private function processArticleRules(ArticleInterface $article, PackageInterface $package, array $destinations): array
    {
        $qb = $this->ruleRepository->createQueryBuilder('r');

        if (!empty($destinations)) {
            $tenants = [];
            foreach ($destinations as $destination) {
                $tenants[] = $destination->getTenant()->getCode();
            }

            $qb
                ->where('r.tenantCode NOT IN (:tenants)')
                ->setParameter('tenants', $tenants);
        }

        $tenantRules = $qb
            ->andWhere('r.tenantCode IS NOT NULL')
            ->orderBy('r.priority', 'desc')
            ->getQuery()
            ->getResult();

        $evaluatedRules = [];
        foreach ((array) $tenantRules as $rule) {
            if ($this->ruleEvaluator->evaluate($rule, $article)) {
                $evaluatedRules[] = $rule;
            }
        }

        return $evaluatedRules;
    }
}
