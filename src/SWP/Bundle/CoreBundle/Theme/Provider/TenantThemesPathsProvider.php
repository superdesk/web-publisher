<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Provider;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;

final class TenantThemesPathsProvider implements TenantThemesPathsProviderInterface
{
    private $tenantContext;

    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    public function getTenantThemesPaths(array $paths): array
    {
        try {
            $tenant = $this->tenantContext->getTenant();
        } catch (TenantNotFoundException $e) {
            $tenant = null;
        }

        if (null !== $tenant) {
            foreach ($paths as $key => $path) {
                $paths[$key] = $path.DIRECTORY_SEPARATOR.$tenant->getCode();
            }
        }

        return $paths;
    }
}
