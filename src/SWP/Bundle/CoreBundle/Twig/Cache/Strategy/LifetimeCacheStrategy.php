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
use SWP\Component\Revision\Context\RevisionContextInterface;

class LifetimeCacheStrategy extends BaseLifetimeCacheStrategy
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var RevisionContextInterface
     */
    protected $revisionContext;

    public function __construct(CacheProviderInterface $cache, TenantContextInterface $tenantContext, RevisionContextInterface $revisionContext)
    {
        $this->tenantContext = $tenantContext;
        $this->revisionContext = $revisionContext;
        parent::__construct($cache);
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($annotation, $value)
    {
        $revisionKey = '';
        if (null !== $revision = $this->revisionContext->getCurrentRevision()) {
            $revisionKey = $revision->getUniqueKey().'__';
        }
        $annotation = $revisionKey.$this->tenantContext->getTenant()->getCode().'__'.$annotation;

        return parent::generateKey($annotation, $value);
    }
}
