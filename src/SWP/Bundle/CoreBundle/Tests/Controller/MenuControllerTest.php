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

    public function testCreateMenuItemsWithTheSameNamesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'politics',
                'label' => 'My first politics menu item',
                'parent' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"politics"', $content);
        self::assertContains('"label":"My first politics menu item"', $content);

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'politics',
                'label' => 'My second politics menu item',
                'parent' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"politics"', $content);
        self::assertContains('"label":"My second politics menu item"', $content);
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
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/menus\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/menus\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/menus\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"level":0,"name":"test","label":"Test","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/1"},"children":{"href":"\/api\/v1\/menus\/1\/children\/"}}},{"id":2,"level":0,"name":"main-menu","label":"Main menu","uri":null,"children":[{"id":3,"level":1,"name":"main-menu-child1","label":"child1","uri":null,"children":[{"id":4,"level":2,"name":"main-menu-child1-child1","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/4"},"children":{"href":"\/api\/v1\/menus\/4\/children\/"},"parent":{"href":"\/api\/v1\/menus\/3"},"root":{"href":"\/api\/v1\/menus\/2"}}}],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"},"children":{"href":"\/api\/v1\/menus\/3\/children\/"},"parent":{"href":"\/api\/v1\/menus\/2"},"root":{"href":"\/api\/v1\/menus\/2"}}}],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"},"children":{"href":"\/api\/v1\/menus\/2\/children\/"}}}]}}', true), $content);
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
        self::assertArraySubset(json_decode('{"id":2,"level":1,"name":"main-menu","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"},"children":{"href":"\/api\/v1\/menus\/2\/children\/"},"parent":{"href":"\/api\/v1\/menus\/1"},"root":{"href":"\/api\/v1\/menus\/1"}}}', true), $content);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => $content['id']]), [
            'menu' => [
                'parent' => null,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":2,"level":0,"name":"main-menu","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"},"children":{"href":"\/api\/v1\/menus\/2\/children\/"}}}', true), $content);
    }

    public function testMenuItemMoveToFirstChildOfParentPosition()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'child1',
                'label' => 'child1',
                'parent' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'child2',
                'label' => 'child2',
                'parent' => 1,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $content['id']]), [
            'menu_move' => [
                'parentId' => 1,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_children_menu', ['id' => 1]));

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":3,"level":1,"name":"child2","label":"child2","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"},"children":{"href":"\/api\/v1\/menus\/3\/children\/"},"parent":{"href":"\/api\/v1\/menus\/1"},"root":{"href":"\/api\/v1\/menus\/1"}}},{"id":2,"level":1,"name":"child1","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"},"children":{"href":"\/api\/v1\/menus\/2\/children\/"},"parent":{"href":"\/api\/v1\/menus\/1"},"root":{"href":"\/api\/v1\/menus\/1"}}}]}}', true), $content);
    }

    public function testMenuItemMoveAfterGivenMenuItem()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'child1',
                'label' => 'child1',
                'parent' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $firstChild = json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
            'menu' => [
                'name' => 'child2',
                'label' => 'child2',
                'parent' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $secondChild = json_decode($client->getResponse()->getContent(), true);

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $firstChild['id']]), [
            'menu_move' => [
                'afterId' => $secondChild['id'],
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_children_menu', ['id' => 1]));

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/menus\/1\/children\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":3,"level":1,"name":"child2","label":"child2","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/3"},"children":{"href":"\/api\/v1\/menus\/3\/children\/"},"parent":{"href":"\/api\/v1\/menus\/1"},"root":{"href":"\/api\/v1\/menus\/1"}}},{"id":2,"level":1,"name":"child1","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v1\/menus\/2"},"children":{"href":"\/api\/v1\/menus\/2\/children\/"},"parent":{"href":"\/api\/v1\/menus\/1"},"root":{"href":"\/api\/v1\/menus\/1"}}}]}}', true), $content);
    }
}
