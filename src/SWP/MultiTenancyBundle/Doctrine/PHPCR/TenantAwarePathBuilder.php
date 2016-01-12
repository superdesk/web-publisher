<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Doctrine\PHPCR;

use PHPCR\Util\PathHelper;
use SWP\MultiTenancyBundle\Context\TenantContextInterface;

class TenantAwarePathBuilder implements TenantAwarePathBuilderInterface
{
    private $tenantContext;

    private $rootPath;

    /**
     * Construct.
     *
     * @param TenantContextInterface $tenantContext Tenant context
     * @param string                 $rootPath      PHPCR root path (e.g. /swp)
     */
    public function __construct(TenantContextInterface $tenantContext, $rootPath)
    {
        $this->tenantContext = $tenantContext;
        $this->rootPath = $rootPath;
        $this->setTenantAwareRootPath();
    }

    /**
     * {@inheritdoc}
     */
    public function build($data, $context = null)
    {
        return $this->absolutize($data, $context);
    }

    /**
     * Absolutize path or paths based on current context when provided.
     *
     * @param string|array $data    Path or paths
     * @param string       $context Path absolute context
     *
     * @return string|array Tenant aware paths
     */
    protected function absolutize($data, $context = null)
    {
        if (is_array($data)) {
            $tenantAwarePaths = [];
            foreach ($data as $path) {
                $tenantAwarePaths[] = $this->absolutizePath($path, $context);
            }

            return $tenantAwarePaths;
        }

        return $this->absolutizePath($data, $context);
    }

    /**
     * Sets PHPCR tree root path to be tenant aware, based on current tenant.
     */
    protected function setTenantAwareRootPath()
    {
        $tenantSubdomain = $this->tenantContext->getTenant()->getSubdomain();
        if ($tenantSubdomain) {
            $this->rootPath = $this->absolutizePath($tenantSubdomain);
        }
    }

    private function absolutizePath($path, $context = null)
    {
        if ($context) {
            $context = $this->rootPath.DIRECTORY_SEPARATOR.$context;
        }

        if ('/' === $path[0]) {
            $path = $context ?: $this->rootPath;
        }

        return PathHelper::absolutizePath($path, $context ?: $this->rootPath);
    }
}
