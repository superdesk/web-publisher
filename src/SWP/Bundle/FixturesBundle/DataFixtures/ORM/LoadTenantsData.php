<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Model\RevisionInterface;

class LoadTenantsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();

        $this->loadFixtures(
            [
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/organization.yml',
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/tenant.yml',
            ]
        );

        $manager->flush();

        $this->loadRevisions();
    }

    private function loadRevisions(): void
    {
        /** @var RevisionManagerInterface $revisionManager */
        $revisionManager = $this->container->get('swp.manager.revision');
        $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');

        if (null === $tenantContext->getTenant()) {
            $tenantContext->setTenant(
                $this->container->get('swp.repository.tenant')->findOneByCode('123abc')
            );
        }

        /** @var RevisionInterface|TenantAwareInterface $firstPublishedRevision */
        $firstTenantPublishedRevision = $revisionManager->create();
        $firstTenantPublishedRevision->setTenantCode('123abc');
        $firstTenantWorkingRevision = $revisionManager->create($firstTenantPublishedRevision);
        $revisionManager->publish($firstTenantPublishedRevision, $firstTenantWorkingRevision);
        $this->addReference('default_tenant_revision', $firstTenantPublishedRevision);

        /** @var RevisionInterface|TenantAwareInterface $firstPublishedRevision */
        $secondTenantPublishedRevision = $revisionManager->create();
        $secondTenantPublishedRevision->setTenantCode('456def');
        $secondTenantWorkingRevision = $revisionManager->create($secondTenantPublishedRevision);
        $revisionManager->publish($secondTenantPublishedRevision, $secondTenantWorkingRevision);

        /** @var RevisionInterface|TenantAwareInterface $firstPublishedRevision */
        $secondTenantPublishedRevision = $revisionManager->create();
        $secondTenantPublishedRevision->setTenantCode('678iop');
        $secondTenantWorkingRevision = $revisionManager->create($secondTenantPublishedRevision);
        $revisionManager->publish($secondTenantPublishedRevision, $secondTenantWorkingRevision);
    }

    public function getOrder(): int
    {
        return -1;
    }
}
