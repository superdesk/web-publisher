<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Resolver;

use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

/**
 * TenantResolver resolves the tenant based on subdomain.
 */
class TenantResolver implements TenantResolverInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * Construct.
     *
     * @param string                    $domain
     * @param TenantRepositoryInterface $tenantRepository
     */
    public function __construct($domain, TenantRepositoryInterface $tenantRepository)
    {
        $this->domain = $domain;
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($host = null)
    {
        if (null === $host) {
            $host = self::DEFAULT_TENANT;
        }

        $subdomain = $this->extractSubdomain($host);
        $tenant = $this->tenantRepository->findOneBySubdomain($subdomain);

        if (null === $tenant) {
            throw new TenantNotFoundException($subdomain);
        }

        return $tenant;
    }

    /**
     * Extracts subdomain from the host.
     *
     * @param string $host Hostname
     *
     * @return string
     */
    protected function extractSubdomain($host)
    {
        if ($this->domain === $host) {
            return self::DEFAULT_TENANT;
        }

        $parts = explode('.', str_replace('.'.$this->domain, '', $host));
        $subdomain = self::DEFAULT_TENANT;
        if (count($parts) === 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
        }

        return $subdomain;
    }
}
