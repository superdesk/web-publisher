<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Model\RevisionInterface;

class LoadRevisionsData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var RevisionManagerInterface $revisionManager */
        $revisionManager = $this->container->get('swp_revision.manager.revision');
        $revisionManager->setObjectManager($this->container->get('swp.object_manager.container'));
        /** @var RevisionInterface $firstPublishedRevision */
        $firstPublishedRevision = $revisionManager->create();
        $revisionManager->publish($firstPublishedRevision);
    }
}
