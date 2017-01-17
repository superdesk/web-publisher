<?php
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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Model\RevisionInterface;

class LoadTenantsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();

        $this->loadFixtures(
            [
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/organization.yml',
                '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/tenant.yml',
            ],
            $manager
        );

        $this->loadRevisions();
    }

    private function loadRevisions()
    {
        /** @var RevisionManagerInterface $revisionManager */
        $revisionManager = $this->container->get('swp_revision.manager.revision');
        $revisionManager->setObjectManager($this->container->get('swp.object_manager.container'));

        /** @var RevisionInterface|TenantAwareInterface $firstPublishedRevision */
        $firstTenantPublishedRevision = $revisionManager->create();
        $firstTenantPublishedRevision->setTenantCode('123abc');
        $revisionManager->publish($firstTenantPublishedRevision);

        /** @var RevisionInterface|TenantAwareInterface $firstPublishedRevision */
        $secondTenantPublishedRevision = $revisionManager->create();
        $secondTenantPublishedRevision->setTenantCode('456def');
        $secondTenantWorkingRevision = $revisionManager->create();
        $secondTenantWorkingRevision->setTenantCode('456def');
        $secondTenantWorkingRevision->setPrevious($secondTenantPublishedRevision);
        $revisionManager->publish($secondTenantPublishedRevision, $secondTenantWorkingRevision);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
