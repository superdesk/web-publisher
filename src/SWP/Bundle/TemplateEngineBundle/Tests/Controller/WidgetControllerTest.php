<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WidgetControllerTest extends WebTestCase
{
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/WidgetModel.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testListWidgetsApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_list_widgets'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/templates\/widgets\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"type":"\\\\SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\WidgetModel\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}},{"id":2,"type":"\\\\SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\WidgetModel\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 2","visible":true,"parameters":{"html_body":"sample widget with html 2"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/2"}}}]}}');
    }

    public function testGetWidgetApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_get_widget', ['id' => 1]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":1,"type":"\\\\SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\WidgetModel\\\\HtmlWidgetHandler","name":"HtmlWidgetHandler number 1","visible":true,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}}');
    }

    public function testCreateWidgetApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_widget'), [
            'widget' => [
                'name'    => 'Simple html widget',
                'visible' => 0,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":3,"type":"\\\\SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\WidgetModel\\\\HtmlWidgetHandler","name":"Simple html widget","visible":false,"parameters":[],"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/3"}}}');
    }

    public function testUpdateWidgetApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_widget', ['id' => 1]), [
            'widget' => [
                'name'       => 'Simple Updated html widget',
                'visible'    => 0,
                'parameters' => [
                    'html_body'   => 'sample widget with <span style=\'color:red\'>html</span>',
                    'extra_param' => 'extra value',
                ],
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '{"id":1,"type":"\\\\SWP\\\\Component\\\\TemplatesSystem\\\\Gimme\\\\WidgetModel\\\\HtmlWidgetHandler","name":"Simple Updated html widget","visible":false,"parameters":{"html_body":"sample widget with <span style=\'color:red\'>html<\/span>","extra_param":"extra value"},"_links":{"self":{"href":"\/api\/v1\/templates\/widgets\/1"}}}');
    }

    public function testDeleteWidgetApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_templates_delete_widget', ['id' => 1]));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '');
    }
}
