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

namespace SWP\Bundle\CoreBundle\Twig\Cache\KeyGenerator;

use SWP\Bundle\ContentBundle\KeyGenerator\MetaKeyGenerator;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

/**
 * Key generator for meta objects.
 */
class TenantAwareMetaKeyGenerator extends MetaKeyGenerator
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * MetaKeyGenerator constructor.
     *
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($meta)
    {
        $tenantCode = $this->tenantContext->getTenant()->getCode();

        return $tenantCode.'_'.parent::generateKey($meta);
    }
}
