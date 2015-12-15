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
    public function __construct(TenantContextInterface $tenantContext, $rootPath)
    {
        $this->tenantContext = $tenantContext;
        $this->rootPath = $rootPath;
    }

    public function build($path)
    {
        $tenantSubdomain = $this->tenantContext->getTenant()->getSubdomain();
        if ($tenantSubdomain) {
            $this->rootPath = $this->rootPath.DIRECTORY_SEPARATOR.$tenantSubdomain;
        }

        if (is_array($path)) {
            return $this->buildPaths($path);
        }

        return $this->absolutizePath($path);
    }

    private function buildPaths($paths)
    {
        $tenantAwarePaths = [];
        foreach ($paths as $path) {
            $tenantAwarePaths[] = $this->absolutizePath($path);
        }

        return $tenantAwarePaths;
    }

    private function absolutizePath($path)
    {
        return PathHelper::absolutizePath($path, $this->rootPath);
    }
}
