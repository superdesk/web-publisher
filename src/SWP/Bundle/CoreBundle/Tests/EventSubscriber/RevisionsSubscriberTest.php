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

use SWP\Bundle\CoreBundle\EventSubscriber\RevisionsSubscriber;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\RevisionBundle\Event\RevisionPublishedEvent;
use SWP\Component\Revision\Manager\RevisionManagerInterface;

class RevisionsSubscriberTest extends WebTestCase
{
    /**
     * @var RevisionManagerInterface
     */
    protected $manager;

    /**
     * @var RevisionsSubscriber
     */
    protected $subscriber;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'container', 'container_widget']);
        $this->manager = $this->getContainer()->get('swp.manager.revision');
        $this->subscriber = new RevisionsSubscriber(
            $this->getContainer()->get('swp.repository.container'),
            $this->getContainer()->get('swp.factory.revision_log')
        );
    }

    public function testInitialization()
    {
        self::assertInstanceOf(RevisionsSubscriber::class, $this->subscriber);
    }

    public function testPublish()
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
        self::assertContains('"updatedAt":null,"isActive":true,"status":"new"', $client->getResponse()->getContent());

        $requestRevisionContext = $client->getContainer()->get('swp_revision.context.revision');
        $revisionContext = $this->getContainer()->get('swp_revision.context.revision');
        $revisionContext->setPublishedRevision($requestRevisionContext->getPublishedRevision());
        $revisionContext->setWorkingRevision($requestRevisionContext->getWorkingRevision());
        $revisionContext->setCurrentRevision($requestRevisionContext->getCurrentRevision());
        $revision = $this->getContainer()->get('swp.object_manager.revision')->merge($revisionContext->getWorkingRevision());
        $this->subscriber->publish(new RevisionPublishedEvent($revision));

        $client->request('GET', $this->getContainer()->get('router')->generate(
            'swp_api_templates_get_container',
            ['uuid' => '5tfdv6resqg']
        ));
        self::assertContains('"isActive":true,"status":"published"', $client->getResponse()->getContent());
    }
}
