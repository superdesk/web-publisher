<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Resolver;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\MultiTenancyBundle\Repository\TenantRepositoryInterface;

/**
 * TenantResolver resolves the tenant based on subdomain.
 */
class TenantResolver implements TenantResolverInterface
{
    const DEFAULT_TENANT = 'default';

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
        $subdomain = $this->extractSubdomain($host);
        $tenant = $this->tenantRepository->findBySubdomain($subdomain);

        if (!$tenant) {
            throw new NotFoundHttpException(sprintf(
                'No site for host "%s", subdomain "%s"',
                $this->domain,
                $subdomain
            ));
        }

        return $tenant;
    }

    /**
     * Extracts subdomain from the host.
     *
     * @param string $host Hostname
     *
     * @return string|null
     */
    protected function extractSubdomain($host)
    {
        $parts = explode('.', str_replace('.'.$this->domain, '', $host));
        $subdomain = self::DEFAULT_TENANT;
        if (count($parts) === 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
        }

        return $subdomain;
    }
}
