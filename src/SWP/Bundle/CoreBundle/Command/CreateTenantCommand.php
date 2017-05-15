<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand as BaseCreateTenantCommand;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Model\RevisionInterface;

class CreateTenantCommand extends BaseCreateTenantCommand
{
    /**
     * {@inheritdoc}
     */
    public function createTenant($domain, $subdomain, $name, $disabled, $organization)
    {
        $tenant = parent::createTenant($domain, $subdomain, $name, $disabled, $organization);
        $this->getContainer()->get('swp_multi_tenancy.tenant_context')->setTenant($tenant);

        $this->getObjectManager()->persist($tenant);
        $this->getObjectManager()->flush();

        /** @var RevisionManagerInterface $revisionManager */
        $revisionManager = $this->getContainer()->get('swp.manager.revision');

        /** @var RevisionInterface $firstTenantPublishedRevision */
        $revision = $revisionManager->create();
        $revision->setTenantCode($tenant->getCode());
        $revisionManager->publish($revision);

        return $tenant;
    }
}
