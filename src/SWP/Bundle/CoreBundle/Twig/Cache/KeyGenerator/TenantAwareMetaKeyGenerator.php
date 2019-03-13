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
use SWP\Component\Revision\Context\RevisionContextInterface;

class TenantAwareMetaKeyGenerator extends MetaKeyGenerator
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var RevisionContextInterface
     */
    protected $revisionContext;

    public function __construct(TenantContextInterface $tenantContext, RevisionContextInterface $revisionContext)
    {
        $this->tenantContext = $tenantContext;
        $this->revisionContext = $revisionContext;
    }

    public function generateKey($meta): string
    {
        $revisionKey = '';
        if (null !== $revision = $this->revisionContext->getCurrentRevision()) {
            $revisionKey = $revision->getUniqueKey().'__';
        }

        return $revisionKey.$this->tenantContext->getTenant()->getCode().'_'.parent::generateKey($meta);
    }
}
