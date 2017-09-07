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

class ContainerControllerTest extends WebTestCase
{
    protected $router;

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

    public function testListContainersApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_list_containers'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"}}}', true), json_decode($client->getResponse()->getContent(), true));
        self::assertArraySubset(json_decode('{"id":1,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true)['_embedded']['_items'][0]);
        self::assertArraySubset(json_decode('{"id":2,"type":1,"name":"Simple Container 2","styles":"border: 1px solid red;","cssClass":"col-md-6","visible":true,"data":[],"widgets":[],"uuid": "3rd56hs342q","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/3rd56hs342q"}}}', true), json_decode($client->getResponse()->getContent(), true)['_embedded']['_items'][1]);
    }

    public function testListContainersApiWhenNoContainers()
    {
        $client = static::createClient();
        $this->loadCustomFixtures(['tenant']);
        $client->request('GET', $this->router->generate('swp_api_templates_list_containers'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"page":1,"limit":10,"pages":1,"total":0,"_links":{"self":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/templates\/containers\/?page=1&limit=10"}},"_embedded":{"_items":[]}}', $client->getResponse()->getContent());
    }

    public function testSingleContainerApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_get_container', ['uuid' => '5tfdv6resqg']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":1,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testUpdateContainerApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_container', ['uuid' => '5tfdv6resqg']), [
            'container' => [
                'name' => 'Simple Container 1',
                'styles' => 'color: #00001',
                'visible' => 0,
                'cssClass' => 'col-md-11',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00001","cssClass":"col-md-11","visible":false,"data":[],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testUpdateSingleContainerPropertyApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_container', ['uuid' => '5tfdv6resqg']), [
            'container' => [
                'styles' => 'color: #00002',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00002","cssClass":"col-md-12","visible":true,"data":[],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testUpdateDataApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_container', ['uuid' => '5tfdv6resqg']), [
            'container' => [
                'data' => [
                    'key_1-test' => 'value_1-test',
                    'key_2-test' => 'value 2',
                ],
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[{"key":"key_1-test","value":"value_1-test"},{"key":"key_2-test","value":"value 2"}],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_templates_update_container', ['uuid' => '5tfdv6resqg']), [
            'container' => [
                'data' => [
                    'test-key' => 'test-value',
                ],
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[{"key":"test-key","value":"test-value"}],"widgets":[],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testLinkingAndUnlinkingWidgetToContainerApi()
    {
        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\\Bundle\\\\TemplatesSystemBundle\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('UNLINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        self::assertArraySubset(json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[],"_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true), json_decode($client->getResponse()->getContent(), true));
        $client->request('GET', $this->router->generate('swp_api_templates_list_widgets', ['id' => 1]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLinkingOnExactPositionApi()
    {
        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\\Bundle\\\\TemplatesSystemBundle\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true),
            json_decode($client->getResponse()->getContent(), true)
        );

        // Move widget 2 on position 1
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/2; rel="widget">,<1; rel="widget-position">',
        ]);

        self::assertArraySubset(
            json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"},{"id":2,"widget":{"id":2,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 2","visible":true,"parameters":{"html_body":"sample widget with html 2"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/2"}}},"position":"1"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true),
            json_decode($client->getResponse()->getContent(), true)
        );

        // Move widget 2 on position 0
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/2; rel="widget">,<0; rel="widget-position">',
        ]);

        self::assertArraySubset(
            json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"1"},{"id":2,"widget":{"id":2,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 2","visible":true,"parameters":{"html_body":"sample widget with html 2"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/2"}}},"position":"0"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true),
            json_decode($client->getResponse()->getContent(), true)
        );

        // Move widget to on last position (1) with parameter: -1
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/2; rel="widget">,<-1; rel="widget-position">',
        ]);

        self::assertArraySubset(
            json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"},{"id":2,"widget":{"id":2,"type":"SWP\\\Bundle\\\\TemplatesSystemBundle\\\Widget\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 2","visible":true,"parameters":{"html_body":"sample widget with html 2"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/2"}}},"position":"1"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true),
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testRenderContainer()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_render_container', ['uuid' => '5tfdv6resqg']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"content":"<div id=\"swp_container_5tfdv6resqg\" class=\"swp_container col-md-12\" style=\"color: #00000\"> <\/div>"}', $client->getResponse()->getContent());

        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode('{"id":3,"type":1,"name":"Simple Container 1","styles":"color: #00000","cssClass":"col-md-12","visible":true,"data":[],"widgets":[{"id":1,"widget":{"id":1,"type":"SWP\\\\Bundle\\\\TemplatesSystemBundle\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},"position":"0"}],"uuid": "5tfdv6resqg","_links":{"self":{"href":"\/api\/v1\/templates\/containers\/5tfdv6resqg"}}}', true),
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('GET', $this->router->generate('swp_api_templates_render_container', ['uuid' => '5tfdv6resqg']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"content":"<div id=\"swp_container_5tfdv6resqg\" class=\"swp_container col-md-12\" style=\"color: #00000\"> <\/div>"}', $client->getResponse()->getContent());

        $client->request('POST', $this->router->generate('swp_api_templates_revision_publish'));

        $client->request('GET', $this->router->generate('swp_api_templates_render_container', ['uuid' => '5tfdv6resqg']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertEquals('{"content":"<div id=\"swp_container_5tfdv6resqg\" class=\"swp_container col-md-12\" style=\"color: #00000\"><div id=\"swp_widget_1\" class=\"swp_widget\">sample widget with <span style=\'color:red\'>html<\/span><\/div><\/div>"}', $client->getResponse()->getContent());
    }

    public function testLinkAlreadyLinkedWidgetApi()
    {
        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testUnLinkUnlinkedWidgetApi()
    {
        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('UNLINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('UNLINK', $this->router->generate('swp_api_templates_link_container', ['uuid' => '5tfdv6resqg']), [], [], [
            'HTTP_LINK' => '</api/v1/templates/widgets/1; rel="widget">',
        ]);
        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }
}
