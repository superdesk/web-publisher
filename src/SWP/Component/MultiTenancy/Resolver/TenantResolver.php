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

use Pdp\Domain;
use Pdp\ResolvedDomainName;
use Pdp\Rules;
use Psr\Cache\InvalidArgumentException;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Filesystem\Filesystem;

class TenantResolver implements TenantResolverInterface
{
    private Rules $publicSuffixList;
    private CacheInterface $cacheProvider;
    private string $suffixListFilename;

    private TenantRepositoryInterface $tenantRepository;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        TenantRepositoryInterface $tenantRepository,
        CacheInterface            $cacheProvider,
        ContainerInterface        $container,
        string                    $suffixListFilename,
    )
    {
        $this->tenantRepository = $tenantRepository;
        $this->cacheProvider = $cacheProvider;
        $this->container = $container;
        $this->suffixListFilename = $suffixListFilename;
        $this->publicSuffixList = Rules::fromString($this->getPublicSuffixList());
    }

    public function resolve(string $host = null): TenantInterface
    {
        $domain = $this->extractDomain($host);
        $subdomain = $this->extractSubdomain($host);

        if (!empty($subdomain)) {
            $tenant = $this->tenantRepository->findOneBySubdomainAndDomain($subdomain, $domain);
        } else {
            $tenant = $this->tenantRepository->findOneByDomain($domain);
        }

        if (null === $tenant) {
            throw new TenantNotFoundException($host);
        }

        return $tenant;
    }

    protected function extractDomain(string $host = null): ?string
    {
        if (null === $host || TenantResolverInterface::LOCALHOST === $host) {
            return TenantResolverInterface::LOCALHOST;
        }

        $result = $this->extractHost($host);

        // handle case for ***.localhost
        if (TenantResolverInterface::LOCALHOST === $result->suffix()->toString() &&
            null !== $result->secondLevelDomain()->toString() &&
            null === $result->subDomain()->toString()
        ) {
            return $result->suffix()->toString();
        }

        $domainString = $result->secondLevelDomain()->toString();
        if (null !== $result->suffix()->toString()) {
            $domainString = $domainString . '.' . $result->suffix()->toString();
        }

        return $domainString;
    }

    protected function extractSubdomain(string $host = null): ?string
    {
        $result = $this->extractHost($host);

        // handle case for ***.localhost
        if (TenantResolverInterface::LOCALHOST === $result->suffix()->toString() &&
            null !== $result->secondLevelDomain()->toString() &&
            null === $result->subDomain()->toString()
        ) {
            return $result->secondLevelDomain()->toString();
        }

        $subdomain = $result->subDomain()->toString();
        if (!empty($subdomain)) {
            return $subdomain;
        }

        return null;
    }

    private function extractHost($host): ResolvedDomainName
    {
        return $this->publicSuffixList->resolve(Domain::fromIDNA2008($host));
    }

    /**
     * We use public suffix list to resolve host. This file should be updated periodically.
     *
     * @see https://github.com/jeremykendall/php-domain-parser
     * @return string
     * @throws InvalidArgumentException
     */
    private function getPublicSuffixList(): string
    {
        return $this->cacheProvider->get('suffix_list', function (ItemInterface $item) {
            $dir = __DIR__ . '/../';
            $filesystem = new Filesystem();
            if (!$filesystem->exists($dir . $this->suffixListFilename)) {
                throw new \LogicException(
                    'Public suffix list file not found. Run swp:public-suffix-list:get command'
                );
            }
            return file_get_contents($dir . $this->suffixListFilename);
        });
    }
}
