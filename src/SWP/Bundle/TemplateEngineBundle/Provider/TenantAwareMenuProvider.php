<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Provider;

use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Menu\Loader\NodeLoader;

class TenantAwareMenuProvider extends PhpcrMenuProvider
{
    public function __construct(NodeLoader $loader,
                                ManagerRegistry $managerRegistry,
                                TenantContext $tenantContext,
                                $menuRoot)
    {
        $tenantId = $tenantContext->getTenant()->getId();
        parent::__construct($loader, $managerRegistry, $menuRoot.'/'.$tenantId);
    }
}
