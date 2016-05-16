<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebRendererBundle\Theme\Helper;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class PathHelper implements PathHelperInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function applySuffixFor(array $paths = [])
    {
        if (empty($paths)) {
            return $paths;
        }

        $contextAwarePaths = [];
        $name = $this->tenantContext->getTenant()->getSubdomain();

        foreach ($paths as $path) {
            $contextAwarePaths[] = rtrim($path, \DIRECTORY_SEPARATOR)
                .\DIRECTORY_SEPARATOR.$name.\DIRECTORY_SEPARATOR;
        }

        return $contextAwarePaths;
    }
}
