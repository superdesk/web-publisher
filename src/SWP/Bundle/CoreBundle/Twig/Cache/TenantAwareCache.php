<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig\Cache;

use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

/**
 * Implements a cache on the filesystem.
 *
 * @author Andrew Tch <andrew@noop.lv>
 */
class TenantAwareCache extends \Twig_Cache_Filesystem
{
    const FORCE_BYTECODE_INVALIDATION = 1;

    private $directory;
    private $themeContext;
    private $tenantContext;

    /**
     * TenantAwareCache constructor.
     *
     * @param string                $directory
     * @param ThemeContextInterface $themeContext
     * @param TenantContext         $tenantContext
     */
    public function __construct(string $directory, ThemeContextInterface $themeContext, TenantContext $tenantContext)
    {
        $this->directory = $directory;
        $this->themeContext = $themeContext;
        $this->tenantContext = $tenantContext;

        parent::__construct($this->directory);
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($name, $className)
    {
        if (null === $this->tenantContext->getTenant()) {
            return parent::generateKey($name, $className);
        }

        $hash = hash('sha256', $className);

        return $this->generateCacheDir().$hash.'.php';
    }

    /**
     * @return string
     */
    public function generateCacheDir()
    {
        $tenantCode = $this->tenantContext->getTenant()->getCode();
        $themeName = str_replace('/', '_', $this->tenantContext->getTenant()->getThemeName());

        return $this->directory.'/'.$tenantCode.'/themes/'.$themeName.'/';
    }
}
