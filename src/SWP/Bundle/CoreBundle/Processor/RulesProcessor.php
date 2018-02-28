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

namespace SWP\Bundle\CoreBundle\Processor;

use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Model\RuleInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class RulesProcessor implements RulesProcessorInterface
{
    public const KEY_ORGANIZATION = 'organization';
    public const KEY_TENANTS = 'tenants';
    public const KEY_TENANT = 'tenant';
    public const KEY_ROUTES = 'routes';
    public const KEY_ROUTE = 'route';

    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var RouteRepositoryInterface
     */
    private $routeRepository;

    /**
     * RulesProcessor constructor.
     *
     * @param TenantRepositoryInterface $tenantRepository
     * @param RouteRepositoryInterface  $routeRepository
     */
    public function __construct(TenantRepositoryInterface $tenantRepository, RouteRepositoryInterface $routeRepository)
    {
        $this->tenantRepository = $tenantRepository;
        $this->routeRepository = $routeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $evaluatedRules): array
    {
        $processedEvaluatedRules = $this->processEvaluatedRules($evaluatedRules);

        $rules = [];
        foreach ($processedEvaluatedRules as $processedEvaluatedRule) {
            foreach ($evaluatedRules as $evaluatedRule) {
                if (null !== $evaluatedRule->getTenantCode()) {
                    $matched = $this->match($processedEvaluatedRule[self::KEY_TENANTS], $evaluatedRule);

                    if (empty($matched)) {
                        continue;
                    }

                    $rules[] = $matched[0];
                }
            }
        }

        return $this->mergeRecursive($processedEvaluatedRules, $rules);
    }

    private function processEvaluatedRules(array $rules): array
    {
        $processedRules = [];

        /** @var RuleInterface $evaluatedRule */
        foreach ($rules as $evaluatedRule) {
            if (null === $evaluatedRule->getTenantCode()) {
                $entry = [self::KEY_ORGANIZATION => $evaluatedRule->getOrganization()];
                $evaluatedRuleConfig = $evaluatedRule->getConfiguration();
                $tenants = [];

                foreach ((array) $evaluatedRuleConfig['destinations'] as $item) {
                    $tenants[][self::KEY_TENANT] = $this->tenantRepository->findOneByCode($item[self::KEY_TENANT]);
                }

                $entry[self::KEY_TENANTS] = $tenants;
                $processedRules[] = $entry;
            }
        }

        return $processedRules;
    }

    private function match(array $tenants, RuleInterface $evaluatedRule): array
    {
        $rules = [];

        foreach ($tenants as $tenant) {
            if ($tenant[self::KEY_TENANT]->getCode() === $evaluatedRule->getTenantCode()) {
                $rules[] = $this->buildRuleArray($evaluatedRule);
            }
        }

        return $rules;
    }

    private function buildRuleArray(RuleInterface $evaluatedRule): array
    {
        return [
            self::KEY_ORGANIZATION => $evaluatedRule->getOrganization(),
            self::KEY_TENANTS => [
                [
                    self::KEY_TENANT => $this->tenantRepository->findOneByCode($evaluatedRule->getTenantCode()),
                    self::KEY_ROUTES => [
                        $this->routeRepository->findOneBy(['id' => $evaluatedRule->getConfiguration()[self::KEY_ROUTE]]),
                    ],
                ],
            ],
        ];
    }

    private function mergeRecursive(array &$firstArray, array &$secondArray): array
    {
        $result = $firstArray;
        foreach ($secondArray as $key => &$value) {
            if (\is_array($value) && isset($result[$key]) && \is_array($result[$key])) {
                $result[$key] = $this->mergeRecursive($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
