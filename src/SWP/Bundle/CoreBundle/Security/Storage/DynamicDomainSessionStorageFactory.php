<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Security\Storage;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class DynamicDomainSessionStorageFactory implements SessionStorageFactoryInterface
{
    private TenantContextInterface $tenantContext;

    private ?\SessionHandlerInterface $handler;

    public function __construct(TenantContextInterface $tenantContext, ?\SessionHandlerInterface $handler = null)
    {
        $this->tenantContext = $tenantContext;
        $this->handler = $handler;
    }

    public function createStorage(?Request $request): SessionStorageInterface
    {
        $storage = new DynamicDomainSessionStorage($this->tenantContext);
        if (null !== $this->handler) {
            $storage->setSaveHandler($this->handler);
        }

        return $storage;
    }
}
