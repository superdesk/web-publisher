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

class TenantAwareMetaKeyGenerator extends MetaKeyGenerator
{
    protected $tenantContext;

    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    public function generateKey($meta): string
    {
        return $this->tenantContext->getTenant()->getCode().'_'.md5(parent::generateKey($meta));
    }
}
