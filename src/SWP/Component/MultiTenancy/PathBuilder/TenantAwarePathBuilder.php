<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\PathBuilder;

use PHPCR\Util\PathHelper;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

/**
 * TenantAwarePathBuilder class.
 */
class TenantAwarePathBuilder implements TenantAwarePathBuilderInterface
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var string
     */
    protected $latestRootPath;

    /**
     * @var string
     */
    protected $defaultRootPath;

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
        $this->defaultRootPath = $rootPath;
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
     * @param string|array $data    Path or array of paths
     * @param string       $context Path absolute context
     *
     * @return string|array Tenant aware paths
     */
    protected function absolutize($data, $context = null)
    {
        $this->makePathTenantAware();
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
     * Makes PHPCR tree root path to be tenant aware.
     *
     * When tenant is not available in tenant context it will resolve
     * the tenant from current request.
     */
    protected function makePathTenantAware()
    {
        if ($this->latestRootPath === $this->rootPath) {
            return;
        }

        $tenant = $this->tenantContext->getTenant();
        $path = $tenant->getCode();

        if (null !== $tenant->getOrganization()) {
            $path = $tenant->getOrganization()->getCode().'/'.$path;
        }

        $this->rootPath = $this->absolutizePath($path);
        $this->latestRootPath = $this->rootPath;
    }

    private function absolutizePath($path, $context = null)
    {
        if (null !== $context) {
            $context = $this->defaultRootPath.DIRECTORY_SEPARATOR.$context;
        }

        if (isset($path[0]) && '/' === $path[0]) {
            $path = $context ?: $this->rootPath;
        }

        return (string) PathHelper::absolutizePath($path, $context ?: $this->rootPath);
    }
}
