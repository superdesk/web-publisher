<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Routing;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RequestContext;

class PwaAwareRouter extends Router
{

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(
        ContainerInterface $container,
        $resource,
        array $options = [],
        RequestContext $context = null,
        ContainerInterface $parameters = null,
        LoggerInterface $logger = null,
        string $defaultLocale = null
    ) {
        $this->tenantContext = $container->get('swp_multi_tenancy.tenant_context');

        parent::__construct($container, $resource, $options, $context, $parameters, $logger, $defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = false)
    {
        $url = parent::generate($name, $parameters, $referenceType);
        if($name === 'swp_user_verify_email') {
            $url = $this->applyPWAUrl($url);
        }
        return $url;
    }

    private function applyPWAUrl (string $url): string {
        if ($this->tenantContext->getTenant() &&
            $this->tenantContext->getTenant()->getPWAConfig() &&
            $this->tenantContext->getTenant()->getPWAConfig()->getUrl()
        ) {
            $PWAUrlParts = parse_url($this->tenantContext->getTenant()->getPWAConfig()->getUrl());
            $urlParts = parse_url($url);
            $scheme = $PWAUrlParts['scheme'] ?? 'https';
            $scheme .= '://';
            $host = $PWAUrlParts['host'] . ':';
            $port = $PWAUrlParts['port'] ?? '';
            $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
            $url = $scheme . $host . $port . $urlParts['path'] . $query;
        }
        return $url;
    }
}
