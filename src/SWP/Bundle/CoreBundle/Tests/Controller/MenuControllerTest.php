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
use Symfony\Component\Routing\RouterInterface;

class MenuControllerTest extends WebTestCase
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
        $this->loadCustomFixtures(['tenant', 'menu']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateMenuApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu',
                'label' => 'Main menu',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"main-menu"', $content);
        self::assertContains('"label":"Main menu"', $content);
    }

    public function testCreateMenuWithTheSameLabelAndNameApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu',
                'label' => 'main-menu',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"main-menu"', $content);
        self::assertContains('"label":"main-menu"', $content);
    }

    public function testGetMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_get_menu', ['id' => 1]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"test"', $content);
    }

    public function testListMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_menu'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"test"', $content);
    }

    public function testUpdateMenuApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => 1]), [
            'menu' => [
                'label' => 'Tested',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"test"', $content);
        self::assertContains('"label":"Tested"', $content);
    }

    public function testDeleteMenuApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_core_delete_menu', ['id' => 1]));

        self::assertEquals(204, $client->getResponse()->getStatusCode());
        self::assertEquals($client->getResponse()->getContent(), '');

        $client->request('GET', $this->router->generate('swp_api_core_get_menu', ['id' => 1]));
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testNestedMenus()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu',
                'label' => 'Main menu',
            ],
        ]);

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu-child1',
                'label' => 'child1',
                'parent' => $rootContent['id'],
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu-child1-child1',
                'label' => 'child1',
                'parent' => $content['id'],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_menu', ['id' => $rootContent['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":4,"_embedded":{"_items":[{"name":"test","label":"Test","uri":null,"id":1,"root":null,"parent":null,"children":[],"lft":1,"rgt":2,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/1"}}},{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[{"name":"main-menu-child1","label":"child1","uri":null,"id":3,"root":null,"parent":null,"children":[{"name":"main-menu-child1-child1","label":"child1","uri":null,"id":4,"root":null,"parent":null,"children":[],"lft":3,"rgt":4,"level":2,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/4"}}}],"lft":2,"rgt":5,"level":1,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"}}}],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},{"name":"main-menu-child1","label":"child1","uri":null,"id":3,"root":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"parent":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"children":[{"name":"main-menu-child1-child1","label":"child1","uri":null,"id":4,"root":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"parent":null,"children":[],"lft":3,"rgt":4,"level":2,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/4"}}}],"lft":2,"rgt":5,"level":1,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"}}},{"name":"main-menu-child1-child1","label":"child1","uri":null,"id":4,"root":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[{"name":"main-menu-child1","label":"child1","uri":null,"id":3,"root":null,"parent":null,"children":[],"lft":2,"rgt":5,"level":1,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"}}}],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"parent":{"name":"main-menu-child1","label":"child1","uri":null,"id":3,"root":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"parent":{"name":"main-menu","label":"Main menu","uri":null,"id":2,"root":null,"parent":null,"children":[],"lft":1,"rgt":6,"level":0,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"}}},"children":[],"lft":2,"rgt":5,"level":1,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"}}},"children":[],"lft":3,"rgt":4,"level":2,"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/4"}}}]}}', true), $content);
    }

    public function testAssigningNotExistingMenuItem()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu-child1-child1',
                'label' => 'child1',
                'parent' => 99999,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"message":"Validation Failed","errors":{"children":{"parent":{"errors":["The selected menu item does not exist!"]}}}}', true), $content);
    }

    public function testParentMenuItemAssignUnassign()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'main-menu',
                'label' => 'child1',
                'parent' => 1,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"name":"main-menu","label":"child1","uri":null,"id":2,"root":{"name":"test","label":"Test","uri":null,"id":1,"root":null,"parent":null,"children":[],"lft":1,"rgt":4,"level":0,"route":null},"parent":{"name":"test","label":"Test","uri":null,"id":1,"root":null,"parent":null,"children":[],"lft":1,"rgt":4,"level":0,"route":null},"children":[],"lft":2,"rgt":3,"level":1,"route":null}', true), $content);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => $content['id']]), [
            'menu' => [
                'parent' => null,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"name":"main-menu","label":"child1","uri":null,"root":null,"parent":null,"children":[],"level":0,"route":null}', true), $content);
    }
}
