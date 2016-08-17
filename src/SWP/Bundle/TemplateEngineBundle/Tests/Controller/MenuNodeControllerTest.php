<?php
/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;

class MenuNodeControllerTest extends WebTestCase
{
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenusData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenuNodesData',
        ], null, 'doctrine_phpcr');

        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateMenuNodeApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_menu_node', ['menuId' => 'test']), [
            'menuNode' => [
                'name' => 'blue',
                'label' => 'Blue',
                'uri' => 'http://example.com/blue',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"id":"\/swp\/123456\/123abc\/menu\/test\/blue"', $content);
    }

    public function testCreateSubMenuNodeApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_templates_create_menu_node', ['menuId' => 'test', 'nodeId' => 'contact/sub']), [
            'menuNode' => [
                'name' => 'subSubContact',
                'label' => 'Sub Sub Contact',
                'uri' => 'http://example.com/contact/sub/sub',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"id":"\/swp\/123456\/123abc\/menu\/test\/contact\/sub\/subSubContact"', $content);
    }

    public function testGetMenuNodeApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_templates_get_menu_node', ['menuId' => 'test', 'nodeId' => 'contact/sub']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"id":"\/swp\/123456\/123abc\/menu\/test\/contact\/sub"', $content);
    }

    public function testListMenuNodesApi()
    {
        $client = static::createClient();
        $route = $this->router->generate('swp_api_templates_list_menu_nodes', ['menuId' => 'test']);
        $client->request('GET', $route);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"id":"\/swp\/123456\/123abc\/menu\/test\/contact\/sub"', $content);
    }

    public function testUpdateMenuNodeApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_menu_node', ['menuId' => 'test', 'nodeId' => 'contact/sub']), [
            'menuNode' => [
                'uri' => 'http://example.com/contact/hub',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"uri":"http:\/\/example.com\/contact\/hub"', $content);
    }

    public function testDeleteMenuNodeApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_templates_delete_menu_node', ['menuId' => 'test', 'nodeId' => 'contact']));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '');

        $client->request('GET', $this->router->generate('swp_api_templates_get_menu_node', ['menuId' => 'test', 'nodeId' => 'contact']));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_templates_get_menu_node', ['menuId' => 'test', 'nodeId' => 'contact/sub']));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
