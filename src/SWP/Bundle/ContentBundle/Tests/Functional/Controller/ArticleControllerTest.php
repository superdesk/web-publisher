<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Controller;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class ArticleControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();
        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData',
            ], 'default'
        );

        $this->router = $this->getContainer()->get('router');
    }

    public function testLoadingArticlesCollection()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('self', $responseArray['_embedded']['_items'][0]['_links']);
        self::assertArrayHasKey('online', $responseArray['_embedded']['_items'][0]['_links']);
    }

    public function testLoadingArticleCustomTemplate()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'template_name' => 'test.html.twig',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(['template_name' => 'test.html.twig'], $responseArray);
        $this->assertTrue(null != $responseArray['updated_at']);
        $this->assertTrue(new \DateTime($responseArray['updated_at']) >= new \DateTime($responseArray['created_at']));
    }

    public function testPublishingArticle()
    {
        $client = static::createClient();
        // unpublish article from fixtures
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'status' => 'new',
            ],
        ]);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(json_decode('{"status":"new"}', true), $responseArray);

        //publish unpublished article
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'status' => 'published',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(['status' => 'published'], $responseArray);
        $this->assertTrue(null != $responseArray['updated_at']);
        $this->assertTrue(new \DateTime($responseArray['updated_at']) >= new \DateTime($responseArray['created_at']));
        $this->assertTrue(new \DateTime($responseArray['published_at']) >= new \DateTime($responseArray['created_at']));
    }

    public function testIfRouteChangedWhenRouteParentWasSwitched()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => null,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => 3,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 3]],
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
            'route' => [
                'name' => 'features',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['id' => 3, 'name' => 'features'],
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 'features']));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 3, 'name' => 'features']],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testUnassigningRouteFromArticle()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => null,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(['route' => null], json_decode($client->getResponse()->getContent(), true));
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => 1,
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['name' => 'news']],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testSwitchingRouteForArticle()
    {
        $client = static::createClient();

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 'test-news-article']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(['route' => ['id' => 1]], json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'test-news-article']), [
            'article' => [
                'route' => 2,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 2, 'name' => 'articles']],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testAssigningNotExistingRouteForArticle()
    {
        $client = static::createClient();

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => 999,
            ],
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
