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

namespace SWP\Bundle\CoreBundle\Twig\Cache\Strategy;

use Asm89\Twig\CacheExtension\CacheProviderInterface;
use Asm89\Twig\CacheExtension\CacheStrategy\LifetimeCacheStrategy as BaseLifetimeCacheStrategy;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class LifetimeCacheStrategy extends BaseLifetimeCacheStrategy
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    public function __construct(CacheProviderInterface $cache, TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
        parent::__construct($cache);
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($annotation, $value)
    {
        $annotation = $this->tenantContext->getTenant()->getCode().'__'.md5($annotation);

        return parent::generateKey($annotation, $value);
    }
}
