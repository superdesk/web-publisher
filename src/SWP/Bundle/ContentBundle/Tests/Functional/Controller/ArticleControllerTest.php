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
        $this->assertArraySubset(['templateName' => 'test.html.twig'], $responseArray);
        $this->assertTrue(null != $responseArray['updatedAt']);
        $this->assertTrue(new \DateTime($responseArray['updatedAt']) >= new \DateTime($responseArray['createdAt']));
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
        $this->assertTrue(null != $responseArray['updatedAt']);
        $this->assertTrue(new \DateTime($responseArray['updatedAt']) >= new \DateTime($responseArray['createdAt']));
        $this->assertTrue(new \DateTime($responseArray['updatedAt']) >= new \DateTime($responseArray['createdAt']));
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

    public function testFilterArticlesByStatus()
    {
        $content = $this->getArticlesByStatus('new');

        self::assertEquals('new', $content['_embedded']['_items'][0]['status']);
        self::assertEquals(1, $content['total']);
        self::assertFalse($content['_embedded']['_items'][0]['isPublishable']);
        self::assertNull($content['_embedded']['_items'][0]['publishedAt']);
        self::assertEquals('Article 1', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByStatus('unpublished');

        self::assertEquals(1, $content['total']);
        self::assertEquals('unpublished', $content['_embedded']['_items'][0]['status']);
        self::assertFalse($content['_embedded']['_items'][0]['isPublishable']);
        self::assertNull($content['_embedded']['_items'][0]['publishedAt']);
        self::assertEquals('Article 2', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByStatus('canceled');

        self::assertEquals(1, $content['total']);
        self::assertEquals('canceled', $content['_embedded']['_items'][0]['status']);
        self::assertFalse($content['_embedded']['_items'][0]['isPublishable']);
        self::assertNull($content['_embedded']['_items'][0]['publishedAt']);
        self::assertEquals('Article 3', $content['_embedded']['_items'][0]['title']);

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', ['status' => ['published', 'canceled']]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(5, $content['total']);

        $content = $this->getArticlesByStatus('published');
        self::assertEquals(4, $content['total']);

        $content = $this->getArticlesByStatus('fake');
        self::assertEquals(0, $content['total']);
    }

    private function getArticlesByStatus($status)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', ['status' => $status]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    public function testFilterArticlesByRoute()
    {
        $content = $this->getArticlesByRouteId(1);
        self::assertEquals(5, $content['total']);

        $content = $this->getArticlesByRouteId(2);
        self::assertEquals(1, $content['total']);
        self::assertEquals(2, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Article 3', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByRouteId(3);
        self::assertEquals(1, $content['total']);
        self::assertEquals(3, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Features client1', $content['_embedded']['_items'][0]['title']);
    }

    public function testFilterArticlesByRouteAndStatus()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [
            'route' => 2,
            'status' => 'canceled',
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(1, $content['total']);
        self::assertEquals(2, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Article 3', $content['_embedded']['_items'][0]['title']);
        self::assertEquals('canceled', $content['_embedded']['_items'][0]['status']);

        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [
            'route' => 2,
            'status' => 'fake',
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(0, $content['total']);
    }

    public function testArticleDelete()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 'features']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_show_articles', ['id' => 'features']));
        self::assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 'features']));
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }

    private function getArticlesByRouteId($routeId)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', ['route' => $routeId]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
