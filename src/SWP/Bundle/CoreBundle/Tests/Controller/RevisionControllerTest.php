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

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;

class RevisionControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'container', 'container_widget']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testRevisionPublishing()
    {
        $client = static::createClient();
        $client->request('GET', $this->getContainer()->get('router')->generate(
            'swp_api_templates_get_container',
            ['uuid' => '5tfdv6resqg']
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('"isActive":true,"status":"published"', $client->getResponse()->getContent());

        $client->request('POST', $this->getContainer()->get('router')->generate('swp_api_templates_revision_publish'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->getContainer()->get('router')->generate(
            'swp_api_templates_get_container',
            ['uuid' => '5tfdv6resqg']
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('"isActive":true,"status":"published"', $client->getResponse()->getContent());
    }

    public function testContainerUpdateAndPublish()
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

        $client->request('POST', $this->getContainer()->get('router')->generate('swp_api_templates_revision_publish'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->getContainer()->get('router')->generate(
            'swp_api_templates_get_container',
            ['uuid' => '5tfdv6resqg']
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('"isActive":true,"status":"published"', $client->getResponse()->getContent());
    }

    public function testLinkWidgetToContainerAndPublish()
    {
        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\\Bundle\\\\TemplatesSystemBundle\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('POST', $this->getContainer()->get('router')->generate('swp_api_templates_revision_publish'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->getContainer()->get('router')->generate(
            'swp_api_templates_get_container',
            ['uuid' => '5tfdv6resqg']
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('"isActive":true,"status":"published"', $client->getResponse()->getContent());
    }
}
