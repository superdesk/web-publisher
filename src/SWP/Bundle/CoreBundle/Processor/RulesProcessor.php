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

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Model\RuleInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class RulesProcessor implements RulesProcessorInterface
{
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
        $tenants = [];

        foreach ($processedEvaluatedRules as $key => $processedEvaluatedRule) {
            foreach ($evaluatedRules as $evaluatedRule) {
                if (null !== $evaluatedRule->getTenantCode()) {
                    if (!empty($processedEvaluatedRule)) {
                        $matched = $this->match((array) $processedEvaluatedRule[self::KEY_TENANTS], $evaluatedRule);

                        if (!empty($matched)) {
                            $tenants[] = $matched;
                        }
                    }
                }
            }
        }

        return $this->merge($processedEvaluatedRules, $tenants);
    }

    private function processEvaluatedRules(array $rules): array
    {
        $processedRules = [
            self::KEY_ORGANIZATION => null,
            self::KEY_TENANTS => [],
        ];

        foreach ($rules as $evaluatedRule) {
            if (null === $evaluatedRule->getTenantCode()) {
                $processedRules[self::KEY_ORGANIZATION] = $evaluatedRule->getOrganization();
                $evaluatedRuleConfig = $evaluatedRule->getConfiguration();

                foreach ((array) $evaluatedRuleConfig['destinations'] as $item) {
                    $processedRules[self::KEY_TENANTS][][self::KEY_TENANT] = $this->tenantRepository->findOneByCode($item[self::KEY_TENANT]);
                }
            }
        }

        return [$processedRules];
    }

    private function match(array $tenants, RuleInterface $evaluatedRule): array
    {
        $tenantsTemp = [];
        $ruleConfig = $evaluatedRule->getConfiguration();
        foreach ($tenants as $tenant) {
            if (isset($tenant[self::KEY_TENANT]) && $tenant[self::KEY_TENANT]->getCode() === $evaluatedRule->getTenantCode()) {
                if (null === $route = $this->findRoute($evaluatedRule)) {
                    continue;
                }

                $tenant[self::KEY_ROUTE] = $route;
                $tenant[self::KEY_FBIA] = isset($ruleConfig[self::KEY_FBIA]) ?? false;
                $tenant[self::KEY_PUBLISHED] = isset($ruleConfig[self::KEY_PUBLISHED]) ?? false;
                $tenant[self::KEY_PAYWALL_SECURED] = isset($ruleConfig[self::KEY_PAYWALL_SECURED]) ?? false;
                $tenant[self::KEY_APPLE_NEWS] = isset($ruleConfig[self::KEY_APPLE_NEWS]) ?? false;
                $tenantsTemp[] = $tenant;
            }
        }

        if (empty($tenantsTemp)) {
            return $tenantsTemp;
        }

        return $tenantsTemp[0];
    }

    private function findRoute(RuleInterface $evaluatedRule): ?RouteInterface
    {
        if (!\array_key_exists(self::KEY_ROUTE, $evaluatedRule->getConfiguration())) {
            return null;
        }

        return $this->routeRepository->findOneBy(['id' => $evaluatedRule->getConfiguration()[self::KEY_ROUTE]]);
    }

    private function merge(array $organizationRules, array $tenants): array
    {
        foreach ($organizationRules as $keyOrg => $processedEvaluatedRule) {
            foreach ($tenants as $tenant) {
                foreach ((array) $processedEvaluatedRule[self::KEY_TENANTS] as $key => $orgTenant) {
                    if (isset($tenant[self::KEY_TENANT]) && isset($orgTenant[self::KEY_TENANT])
                        && $tenant[self::KEY_TENANT]->getCode() === $orgTenant[self::KEY_TENANT]->getCode()) {
                        $organizationRules[$keyOrg][self::KEY_TENANTS][$key] = $tenant;
                    }
                }
            }
        }

        return $organizationRules[0];
    }
}
