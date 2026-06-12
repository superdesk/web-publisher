<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use Doctrine\Persistence\ManagerRegistry;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates as BasePrefixCandidates;
use Symfony\Cmf\Component\Routing\Candidates\CandidatesInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tenant aware candidates for PHPCR routing: prefixes are resolved through
 * the tenant aware path builder on every call. Decorates the (final) CMF
 * PrefixCandidates instead of extending it.
 */
class PrefixCandidates implements CandidatesInterface
{
    /**
     * @var BasePrefixCandidates
     */
    protected $inner;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var array
     */
    protected $routePathsNames = [];

    public function __construct(array $prefixes = [], array $locales = [], ?ManagerRegistry $doctrine = null, int $limit = 20)
    {
        $this->inner = new BasePrefixCandidates($prefixes, $locales, $doctrine, $limit);
    }

    public function isCandidate(string $name): bool
    {
        $this->refreshPrefixes();

        return $this->inner->isCandidate($name);
    }

    public function restrictQuery(object $queryBuilder): void
    {
        $this->refreshPrefixes();

        $this->inner->restrictQuery($queryBuilder);
    }

    public function getCandidates(Request $request): array
    {
        $this->refreshPrefixes();

        return $this->inner->getCandidates($request);
    }

    public function getPrefixes(): array
    {
        $this->refreshPrefixes();

        return $this->inner->getPrefixes();
    }

    public function setPrefixes(array $prefixes): void
    {
        $this->inner->setPrefixes($prefixes);
    }

    public function setManagerName($manager): void
    {
        $this->inner->setManagerName($manager);
    }

    /**
     * Sets path builder.
     */
    public function setPathBuilder(TenantAwarePathBuilderInterface $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    public function setRoutePathsNames(array $routePathsNames = [])
    {
        $this->routePathsNames = $routePathsNames;
    }

    private function refreshPrefixes(): void
    {
        if (null !== $this->pathBuilder) {
            $this->inner->setPrefixes((array) $this->pathBuilder->build($this->routePathsNames));
        }
    }
}
