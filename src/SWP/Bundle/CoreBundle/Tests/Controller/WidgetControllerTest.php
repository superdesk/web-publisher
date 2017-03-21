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

use SWP\Component\Common\Pagination\PaginationInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;

class WidgetControllerTest extends WebTestCase
{
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/WidgetModel.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testListWidgetsApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_list_widgets'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},{"id":2,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 2","visible":true,"parameters":{"html_body":"sample widget with html 2"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/2"}}}]}}');
    }

    public function testListWidgetsApiWhenNoWidgets()
    {
        $client = static::createClient();

        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles([], true);

        $client->request('GET', $this->router->generate('swp_api_templates_list_widgets'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals($client->getResponse()->getContent(), '{"page":1,"limit":10,"pages":1,"total":0,"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"}},"_embedded":{"_items":[]}}');
    }

    public function testListWidgetsApiPagination()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_list_widgets', [PaginationInterface::LIMIT_PARAMETER_NAME => 1]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"page":1,"limit":1,"pages":2,"total":2,"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=1"},"first":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=1"},"last":{"href":"\/api\/v1\/templates\/widgets\/?page=2&limit=1"},"next":{"href":"\/api\/v1\/templates\/widgets\/?page=2&limit=1"}},"_embedded":{"_items":[{"id":1,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}}]}}', $client->getResponse()->getContent());
    }

    public function testGetWidgetApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_get_widget', ['id' => 1]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":1,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}}');
    }

    public function testCreateWidgetApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_widget'), [
            'widget' => [
                'name' => 'Simple html widget',
                'visible' => false,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":3,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"Simple html widget","visible":false,"parameters":[],"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/3"}}}');

        $client->request('POST', $this->router->generate('swp_api_templates_create_widget'), [
            'widget' => [
                'name' => 'Simple html widget',
                'visible' => false,
            ],
        ]);

        $this->assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testCreateContentListWidgetApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_widget'), [
            'widget' => [
                'name' => 'Simple list widget',
                'visible' => false,
                'type' => 4,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":3,"type":"SWP\\\\Bundle\\\\CoreBundle\\\\Widget\\\\ContentListWidget","name":"Simple list widget","visible":false,"parameters":[],"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/3"}}}');
    }

    public function testCreateWidgetByHisClassApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_widget'), [
            'widget' => [
                'name' => 'Simple list widget',
                'visible' => false,
                'type' => 'SWP\\Bundle\\CoreBundle\\Widget\\ContentListWidget',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":3,"type":"SWP\\\\Bundle\\\\CoreBundle\\\\Widget\\\\ContentListWidget","name":"Simple list widget","visible":false,"parameters":[],"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/3"}}}');
    }

    public function testUpdateWidgetApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_widget', ['id' => 1]), [
            'widget' => [
                'name' => 'Simple Updated html widget',
                'visible' => false,
                'parameters' => [
                    'html_body' => 'sample widget with <span style=\'color:red\'>html</span>',
                    'extra_param' => 'extra value',
                ],
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":1,"type":"SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\Widget\\\\HtmlWidgetHandler","name":"Simple Updated html widget","visible":false,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>","extra_param":"extra value"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}}');
    }

    public function testUpdateFakeWidget()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_widget', ['id' => 99999]), [
            'widget' => [
                'name' => 'Simple Updated html widget',
                'visible' => false,
                'parameters' => [
                    'html_body' => 'sample widget with <span style=\'color:red\'>html</span>',
                    'extra_param' => 'extra value',
                ],
            ],
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteWidgetApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_templates_delete_widget', ['id' => 1]));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '');

        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_templates_delete_widget', ['id' => 9999]));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
