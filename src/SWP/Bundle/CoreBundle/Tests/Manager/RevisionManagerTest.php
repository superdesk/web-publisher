<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Model\RevisionInterface;

class RevisionManagerTest extends WebTestCase
{
    /**
     * @var RevisionManagerInterface
     */
    protected $manager;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'container', 'container_widget']);
        $this->manager = $this->getContainer()->get('swp.manager.revision');
    }

    public function testCreateInitialRevision()
    {
        self::assertInstanceOf(RevisionInterface::class, $this->manager->create());
    }

    public function testCreateRevision()
    {
        $defaultRevision = $this->manager->create();
        $revision = $this->manager->create($defaultRevision);
        self::assertInstanceOf(RevisionInterface::class, $revision);
        self::assertEquals($defaultRevision, $revision->getPrevious());
    }

    public function testModifyContainerAndPublishRevision()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->getContainer()->get('router')->generate(
            'swp_api_templates_update_container',
            ['uuid' => '5tfdv6resqg']
        ), [
            'container' => [
                'name' => 'Simple Container 23',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 23","uuid": "5tfdv6resqg"}', true), json_decode($client->getResponse()->getContent(), true));

        $requestRevisionContext = $client->getContainer()->get('swp_revision.context.revision');
        $revisionContext = $this->getContainer()->get('swp_revision.context.revision');
        $revisionContext->setPublishedRevision($requestRevisionContext->getPublishedRevision());
        $revisionContext->setWorkingRevision($requestRevisionContext->getWorkingRevision());
        $revisionContext->setCurrentRevision($requestRevisionContext->getCurrentRevision());

        $revision = $this->getContainer()->get('swp.object_manager.revision')->merge($revisionContext->getWorkingRevision());
        $this->manager->publish($revision);
    }
}
