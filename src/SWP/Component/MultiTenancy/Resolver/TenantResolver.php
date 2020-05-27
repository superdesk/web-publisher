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

use LayerShifter\TLDExtract\Extract;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

class TenantResolver implements TenantResolverInterface
{
    private $tenantRepository;

    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    public function resolve(string $host = null): TenantInterface
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

    protected function extractDomain(string $host = null): string
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

    protected function extractSubdomain(string $host = null): ?string
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

        return null;
    }

    private function extractHost($host)
    {
        $extract = new Extract();

        return $extract->parse($host);
    }
}
