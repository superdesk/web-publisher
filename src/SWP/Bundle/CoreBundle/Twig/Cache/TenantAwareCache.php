<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig\Cache;

use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use Twig\Cache\FilesystemCache;

class TenantAwareCache extends FilesystemCache implements TenantAwareCacheInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var TenantContext
     */
    private $tenantContext;

    public function __construct(string $directory, TenantContext $tenantContext)
    {
        $this->directory = $directory;
        $this->tenantContext = $tenantContext;

        parent::__construct($this->directory);
    }

    public function generateKey($name, $className)
    {
        if (null === $this->tenantContext->getTenant()) {
            return parent::generateKey($name, $className);
        }

        $hash = hash('sha256', $className);

        return $this->generateCacheDir().$hash.'.php';
    }

    public function generateCacheDir()
    {
        $tenantCode = $this->tenantContext->getTenant()->getCode();
        $themeName = str_replace('/', '_', $this->tenantContext->getTenant()->getThemeName());

        return $this->directory.'/'.$tenantCode.'/themes/'.$themeName.'/';
    }
}
