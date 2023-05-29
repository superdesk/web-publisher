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

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class MenuControllerTest extends WebTestCase
{
    use ArraySubsetAsserts;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'menu']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateMenuApi()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu',
                'label' => 'Main menu',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        self::assertStringContainsString('"name":"main-menu"', $content);
        self::assertStringContainsString('"label":"Main menu"', $content);
    }

    public function testCreateMenuAndModifyRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'templateName' => 'test.html.twig',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu',
                'label' => 'Main menu',
                'route' => 3,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        $content = json_decode($content, true);
        $client->request('GET', $content['uri']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'name' => 'simple-edited-test-route',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_get_menu', ['id' => 2]));
        $content = json_decode($client->getResponse()->getContent(), true);
        $client->request('GET', $content['uri']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateMenuItemsWithTheSameNamesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'politics',
                'label' => 'My first politics menu item',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"politics"', $content);
        self::assertStringContainsString('"label":"My first politics menu item"', $content);

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'politics',
                'label' => 'My second politics menu item',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"politics"', $content);
        self::assertStringContainsString('"label":"My second politics menu item"', $content);
    }

    public function testCreateMenuWithTheSameLabelAndNameApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu',
                'label' => 'main-menu',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"main-menu"', $content);
        self::assertStringContainsString('"label":"main-menu"', $content);
    }

    public function testGetMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_get_menu', ['id' => 1]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"test"', $content);
    }

    public function testListMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_menu'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"test"', $content);
    }

    public function testUpdateMenuApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => 1]), [
                'label' => 'Tested',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"name":"test"', $content);
        self::assertStringContainsString('"label":"Tested"', $content);
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
                'name' => 'main-menu',
                'label' => 'Main menu',
        ]);

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu-child1',
                'label' => 'child1',
                'parent' => $rootContent['id'],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu-child1-child1',
                'label' => 'child1',
                'parent' => $content['id'],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_get_menu', ['id' => $rootContent['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":2,"root":2,"level":0,"name":"main-menu","label":"Main menu","uri":null,"children":[{"id":3,"root":2,"level":1,"name":"main-menu-child1","label":"child1","uri":null,"children":[{"id":4,"root":2,"level":2,"name":"main-menu-child1-child1","label":"child1","uri":null,"children":[],"parent":3,"position":0,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/4"},"children":{"href":"\/api\/v2\/menus\/4\/children\/"},"parent":{"href":"\/api\/v2\/menus\/3"},"root":{"href":"\/api\/v2\/menus\/2"}}}],"parent":2,"position":0,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/3"},"children":{"href":"\/api\/v2\/menus\/3\/children\/"},"parent":{"href":"\/api\/v2\/menus\/2"},"root":{"href":"\/api\/v2\/menus\/2"}}}],"parent":null,"position":1,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"}}}', true), $content);
    }

    public function testAssigningNotExistingMenuItem()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu-child1-child1',
                'label' => 'child1',
                'parent' => 99999,
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"message":"Validation Failed","errors":{"children":{"parent":{"errors":["The selected menu item does not exist!"]}}}}', true), $content);
    }

    public function testParentMenuItemAssignUnassign()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'main-menu',
                'label' => 'child1',
                'parent' => 1,
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":2,"level":1,"name":"main-menu","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}}', true), $content);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => $content['id']]), [
                'parent' => null,
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":2,"level":0,"name":"main-menu","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"}}}', true), $content);
    }

    public function testMoveMenuItemToFirstPositionUnderParent()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'child1',
                'label' => 'child1',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'child2',
                'label' => 'child2',
                'parent' => 1,
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $content['id']]), [
                'parent' => 1,
                'position' => 0,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_children_menu', ['id' => 1]));

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":3,"level":1,"name":"child2","label":"child2","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/3"},"children":{"href":"\/api\/v2\/menus\/3\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}},{"id":2,"level":1,"name":"child1","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}}]}}', true), $content);
    }

    public function testMoveMenuItemFromFirstPositionToFirstPositionUnderParent()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'menu 1',
                'label' => 'menu 1',
                'parent' => null,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => 1]), [
                'parent' => 2,
                'position' => 0,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_menu'));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['_embedded']['_items'][0]['id'], 2);
        self::assertEquals($content['_embedded']['_items'][0]['children'][0]['id'], 1);
    }

    public function testMoveMenuItemFromFirstToSecondPositionInParentSubtree()
    {
        $client = static::createClient();
        $firstChild = $this->assertCreatingChildren();

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $firstChild['id']]), [
                'parent' => 1,
                'position' => 1,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_children_menu', ['id' => 1]));

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":3,"level":1,"name":"child2","label":"child2","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/3"},"children":{"href":"\/api\/v2\/menus\/3\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}},{"id":2,"level":1,"name":"child1","label":"child1","uri":null,"children":[],"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}}]}}', true), $content);
    }

    public function testMoveMenuItemFromLastToSecondPosition()
    {
        $client = static::createClient();
        $this->assertCreatingChildren();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'child3',
                'label' => 'child3',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $lastChild = json_decode($client->getResponse()->getContent(), true);

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $lastChild['id']]), [
                'parent' => 1,
                'position' => 1,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_children_menu', ['id' => 1]));
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":3,"_links":{"self":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/menus\/1\/children\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":2,"level":1,"name":"child1","label":"child1","uri":null,"children":[],"position":0,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/2"},"children":{"href":"\/api\/v2\/menus\/2\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}},{"id":4,"level":1,"name":"child3","label":"child3","uri":null,"children":[],"position":1,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/4"},"children":{"href":"\/api\/v2\/menus\/4\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}},{"id":3,"level":1,"name":"child2","label":"child2","uri":null,"children":[],"position":2,"route":null,"_links":{"self":{"href":"\/api\/v2\/menus\/3"},"children":{"href":"\/api\/v2\/menus\/3\/children\/"},"parent":{"href":"\/api\/v2\/menus\/1"},"root":{"href":"\/api\/v2\/menus\/1"}}}]}}', true), $content);
    }

    public function testMoveMenuItemAtTheSamePositionAsItCurrentlyIs()
    {
        $client = static::createClient();
        $firstChild = $this->assertCreatingChildren();

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $firstChild['id']]), [
                'parent' => 1,
                'position' => 0,
        ]);

        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testMoveMenuItemAtNotValidPosition()
    {
        $client = static::createClient();
        $firstChild = $this->assertCreatingChildren();

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $firstChild['id']]), [
                'parent' => 1,
                'position' => 99,
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testMoveMenuItemIfParentDoesntExist()
    {
        $client = static::createClient();
        $firstChild = $this->assertCreatingChildren();

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => $firstChild['id']]), [
                'parent' => 9999,
                'position' => 1,
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testMoveMenuItemIfMovedItemDoesntExist()
    {
        $client = static::createClient();
        $this->assertCreatingChildren();

        $client->request('PATCH', $this->router->generate('swp_api_core_move_menu', ['id' => 9999]), [
                'parent' => 1,
                'position' => 1,
        ]);

        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }

    private function assertCreatingChildren()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'child1',
                'label' => 'child1',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $firstChild = json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'child2',
                'label' => 'child2',
                'parent' => 1,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        return $firstChild;
    }

    public function testAssignRouteToMenuApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_menu'), [
                'name' => 'navigation',
                'label' => 'Navigation',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        self::assertStringContainsString('"name":"navigation"', $content);
        self::assertStringContainsString('"label":"Navigation"', $content);
        self::assertStringContainsString('"route":null', $content);

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'my-menu-route',
                'type' => 'collection',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => 1]), [
                'route' => $content['id'],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
//        self::assertContains('"route":3', $content);
    }

    public function testAssingRouteToMenuAndRemoveRoute()
    {
        self::testAssignRouteToMenuApi();

        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 3]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_core_update_menu', ['id' => 1]), [
                'route' => null,
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 3]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
