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
        foreach ($processedEvaluatedRules as $key => $processedEvaluatedRule) {
            foreach ($evaluatedRules as $evaluatedRule) {
                if (null !== $evaluatedRule->getTenantCode()) {
                    $rules[$key] = $processedEvaluatedRule;

                    if (self::KEY_TENANTS !== $key) {
                        continue;
                    }

                    $rules[$key] = $this->match((array) $processedEvaluatedRule, $evaluatedRule);
                }
            }
        }

        return $this->merge([$processedEvaluatedRules], [$rules]);
    }

    private function processEvaluatedRules(array $rules): array
    {
        $processedRules = [];

        foreach ($rules as $evaluatedRule) {
            if (null === $evaluatedRule->getTenantCode()) {
                $processedRules[self::KEY_ORGANIZATION] = $evaluatedRule->getOrganization();
                $evaluatedRuleConfig = $evaluatedRule->getConfiguration();
                foreach ((array) $evaluatedRuleConfig['destinations'] as $item) {
                    $processedRules[self::KEY_TENANTS][][self::KEY_TENANT] = $this->tenantRepository->findOneByCode($item[self::KEY_TENANT]);
                }
            }
        }

        return $processedRules;
    }

    private function match(array $tenants, RuleInterface $evaluatedRule): array
    {
        $tenantsTemp = [];
        foreach ($tenants as $tenant) {
            if ($tenant[self::KEY_TENANT]->getCode() === $evaluatedRule->getTenantCode()) {
                if (null === $route = $this->findRoute($evaluatedRule)) {
                    continue;
                }

                $tenant[self::KEY_ROUTES][] = $route;
                $tenantsTemp[] = $tenant;
            }
        }

        return $tenantsTemp;
    }

    private function findRoute(RuleInterface $evaluatedRule): ?RouteInterface
    {
        return $this->routeRepository->findOneBy(['id' => $evaluatedRule->getConfiguration()[self::KEY_ROUTE]]);
    }

    private function merge(array $organizationRules, array $tempRules): array
    {
        foreach ($organizationRules as $keyOrg => $processedEvaluatedRule) {
            foreach ($tempRules as $rule) {
                if (!isset($rule[self::KEY_TENANTS])) {
                    continue;
                }

                foreach ((array) $rule[self::KEY_TENANTS] as $tenant) {
                    foreach ((array) $processedEvaluatedRule[self::KEY_TENANTS] as $key => $orgTenant) {
                        if ($tenant[self::KEY_TENANT]->getCode() === $orgTenant[self::KEY_TENANT]->getCode()) {
                            $organizationRules[$keyOrg][self::KEY_TENANTS][$key] = $tenant;
                        }
                    }
                }
            }
        }

        return $organizationRules[0];
    }
}
