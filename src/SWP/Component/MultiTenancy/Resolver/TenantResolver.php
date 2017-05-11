<?php

/*
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
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * TenantResolver constructor.
     *
     * @param TenantRepositoryInterface $tenantRepository
     */
    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($host = null)
    {
        $domain = $this->extractDomain($host);
        $subdomain = $this->extractSubdomain($host);

        if (null !== $subdomain) {
            $tenant = $this->tenantRepository->findOneBySubdomainAndDomain($subdomain, $domain);
        } else {
            $tenant = $this->tenantRepository->findOneByDomain($domain);
        }

        if (null === $tenant) {
            throw new TenantNotFoundException($host);
        }

        return $tenant;
    }

    /**
     * @param $host
     *
     * @return string
     */
    protected function extractDomain($host)
    {
        if (null === $host || TenantResolverInterface::LOCALHOST === $host) {
            return TenantResolverInterface::LOCALHOST;
        }

        $result = $this->extractHost($host);

        // handle case for ***.localhost
        if (TenantResolverInterface::LOCALHOST === $result->getSuffix() &&
            null !== $result->getHostname() &&
            null === $result->getSubdomain()
        ) {
            return $result->getSuffix();
        }

        $domainString = $result->getHostname();
        if (null !== $result->getSuffix()) {
            $domainString = $domainString.'.'.$result->getSuffix();
        }

        return $domainString;
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
        $result = $this->extractHost($host);

        // handle case for ***.localhost
        if (TenantResolverInterface::LOCALHOST === $result->getSuffix() &&
            null !== $result->getHostname() &&
            null === $result->getSubdomain()
        ) {
            return $result->getHostname();
        }

        $subdomain = $result->getSubdomain();
        if (null !== $subdomain) {
            return $subdomain;
        }

        return;
    }

    private function extractHost($host)
    {
        $extract = new \LayerShifter\TLDExtract\Extract();

        return $extract->parse($host);
    }
}
