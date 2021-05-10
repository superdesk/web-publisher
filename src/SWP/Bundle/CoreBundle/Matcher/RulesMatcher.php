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
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\CoreBundle\Processor\RulesProcessorInterface;
use SWP\Bundle\CoreBundle\Provider\PublishDestinationProviderInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Rule\Evaluator\RuleEvaluatorInterface;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
        $article->setPackage($package);

        $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

        $destinations = $this->publishDestinationProvider->getDestinations($package);
        $evaluatedOrganizationRules = $this->processPackageRules($package);
        $evaluatedRules = $this->processArticleRules($article, $destinations);
        $processedRules = $this->rulesProcessor->process(array_merge($evaluatedOrganizationRules, $evaluatedRules));
        $result = $this->process($processedRules, $destinations);

        $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);

        return $result;
    }

    private function process(array $processedRules, array $destinations): array
    {
        if (empty((array) $processedRules['tenants'])) {
            foreach ($destinations as $destination) {
                $processedRules['organization'] = $destination->getOrganization();
                $processedRules['tenants'][] = $this->createTenantArrayFromDestination($destination);
            }

            return $processedRules;
        }

        if (empty((array) $processedRules['tenants'])) {
            return [];
        }

        $rules = [];
        foreach ($destinations as $destinationKey => $destination) {
            $rules['organization'] = $destination->getOrganization();
            $rules['tenants'][] = $this->createTenantArrayFromDestination($destination);
        }

        if (empty($rules)) {
            return $processedRules;
        }

        foreach ((array) $rules['tenants'] as $key => $rule) {
            foreach ((array) $processedRules['tenants'] as $tenant) {
                if ($tenant['tenant'] === $rule['tenant']) {
                    $rules['tenants'][$key] = $rule;
                } else {
                    $rules['tenants'][] = $tenant;
                }
            }
        }

        $ids = array_column($rules['tenants'], 'tenant');
        $ids = array_unique($ids);
        $tenants = array_filter($rules['tenants'], static function ($key, $value) use ($ids) {
            return array_key_exists($value, $ids);
        }, ARRAY_FILTER_USE_BOTH);

        $rules['tenants'] = $tenants;

        return $rules;
    }

    private function createTenantArrayFromDestination(PublishDestinationInterface $destination): array
    {
        return [
            'tenant' => $destination->getTenant(),
            'route' => $destination->getRoute(),
            'is_published_fbia' => $destination->isPublishedFbia(),
            'published' => $destination->isPublished(),
            'paywall_secured' => $destination->isPaywallSecured(),
            'content_lists' => $destination->getContentLists(),
            'is_published_to_apple_news' => $destination->isPublishedToAppleNews(),
        ];
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

    private function processArticleRules(ArticleInterface $article, array $destinations): array
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
