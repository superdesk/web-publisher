<?php
/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
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

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"name":"main-menu"', $content);
        self::assertContains('"label":"Main menu"', $content);
    }

    /*public function testGetMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_menu'));

        dump($client->getResponse()->getContent());die;

        //self::assertEquals(200, $client->getResponse()->getStatusCode());
        //$content = $client->getResponse()->getContent();
       // self::assertContains('"name":"test"', $content);
    }*/

    public function testListMenuApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_menu'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"name":"test"', $content);
    }
/*
    public function testUpdateMenuApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_templates_update_menu', ['id' => 'test']), [
            'menu' => [
                'label' => 'Tested',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertContains('"name":"test"', $content);
        $this->assertContains('"label":"Tested"', $content);
    }

    public function testDeleteMenuApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_templates_delete_menu', ['id' => 'test']));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '');

        $client->request('GET', $this->router->generate('swp_api_templates_get_menu', ['id' => 'test']));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }*/
}
