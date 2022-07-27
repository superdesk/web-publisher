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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RouteControllerTest extends WebTestCase
{
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
        $this->loadCustomFixtures(['tenant', 'article']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testRemoveParentRoute()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 3]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', ['id' => 6]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(ArticleInterface::STATUS_PUBLISHED, $content['status']);

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(PackageInterface::STATUS_PUBLISHED, $content['status']);

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 6]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(ArticleInterface::STATUS_NEW, $content['status']);

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(PackageInterface::STATUS_USABLE, $content['status']);
    }
}
