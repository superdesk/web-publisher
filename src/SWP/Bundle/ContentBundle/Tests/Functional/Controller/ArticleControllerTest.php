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

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData;

class ArticleControllerTest extends WebTestCase
{
    use ArraySubsetAsserts;
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->initDatabase();
        $this->loadFixtures(
            [
                LoadArticlesData::class,
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
                'template_name' => 'test.html.twig',
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
                'status' => 'new',
        ]);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        self::assertArraySubset(json_decode('{"status":"new"}', true), $responseArray);
        $publishedAt = $responseArray['published_at'];
        sleep(1);
        //publish unpublished article
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
                'status' => 'published',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        self::assertArraySubset(['status' => 'published'], $responseArray);
        self::assertTrue(null != $responseArray['updated_at']);
        self::assertTrue(new \DateTime($responseArray['updated_at']) >= new \DateTime($responseArray['created_at']));
        self::assertTrue(new \DateTime($responseArray['updated_at']) >= new \DateTime($responseArray['created_at']));
    }

    public function testIfRouteChangedWhenRouteParentWasSwitched()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
                'route' => null,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
                'route' => 3,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 3]],
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'name' => 'features',
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
                'route' => null,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(['route' => null], json_decode($client->getResponse()->getContent(), true));
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
                'route' => 1,
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
                'route' => 2,
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
                'route' => 999,
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testFilterArticlesByStatus()
    {
        $content = $this->getArticlesByStatus('new');

        self::assertEquals('new', $content['_embedded']['_items'][0]['status']);
        self::assertEquals(1, $content['total']);
        self::assertFalse($content['_embedded']['_items'][0]['is_publishable']);
        self::assertNull($content['_embedded']['_items'][0]['published_at']);
        self::assertEquals('Article 1', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByStatus('unpublished');

        self::assertEquals(1, $content['total']);
        self::assertEquals('unpublished', $content['_embedded']['_items'][0]['status']);
        self::assertFalse($content['_embedded']['_items'][0]['is_publishable']);
        self::assertNull($content['_embedded']['_items'][0]['published_at']);
        self::assertEquals('Article 2', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByStatus('canceled');

        self::assertEquals(1, $content['total']);
        self::assertEquals('canceled', $content['_embedded']['_items'][0]['status']);
        self::assertFalse($content['_embedded']['_items'][0]['is_publishable']);
        self::assertNull($content['_embedded']['_items'][0]['published_at']);
        self::assertEquals('Article 3', $content['_embedded']['_items'][0]['title']);

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', ['status' => ['published', 'canceled']]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(6, $content['total']);

        $content = $this->getArticlesByStatus('published');
        self::assertEquals(5, $content['total']);

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
        self::assertEquals(3, $content['total']);

        $content = $this->getArticlesByRouteId(2, ArticleInterface::STATUS_CANCELED);
        self::assertEquals(1, $content['total']);
        self::assertEquals(2, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Article 3', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByRouteId(3);
        self::assertEquals(1, $content['total']);
        self::assertEquals(3, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Features client1', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByRouteId(4);
        self::assertEquals(1, $content['total']);
        self::assertEquals(4, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Lifestyle article 1', $content['_embedded']['_items'][0]['title']);

        $content = $this->getArticlesByRouteId(2, ArticleInterface::STATUS_PUBLISHED, true);
        self::assertEquals(1, $content['total']);
        self::assertEquals(4, $content['_embedded']['_items'][0]['route']['id']);
        self::assertEquals('Lifestyle article 1', $content['_embedded']['_items'][0]['title']);
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

    public function testFilterArticlesByAuthor()
    {
        $content = $this->getArticlesByAuthor('Jhon Doe');
        self::assertEquals(0, $content['total']);

        $content = $this->getArticlesByAuthor('John Doe');
        self::assertEquals(8, $content['total']);
    }

    public function testFilterArticlesBySource()
    {
        $content = $this->getArticlesBySource('aap');
        self::assertEquals(1, $content['total']);

        $content = $this->getArticlesBySource('ntb');
        self::assertEquals(0, $content['total']);
    }

    public function testFilterArticlesByDate()
    {
        $date = '2017-04-05 12:12:00';
        $content = $this->getArticlesByPublicationDate($date, 'publishedAfter');
        self::assertEquals(4, $content['total']);

        $now = new \DateTime('now');
        $now->modify('-1 minute');
        $content = $this->getArticlesByPublicationDate($now->format('Y-m-d H:i:s'), 'publishedBefore');
        self::assertEquals(1, $content['total']);

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [
            'publishedAfter' => '2017-04-05 12:10:00',
            'publishedBefore' => '2017-04-05 12:15:00',
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testFilteringArticlesByTitle()
    {
        $content = $this->getArticlesByTitle('Features client1');
        self::assertEquals(1, $content['total']);

        $content = $this->getArticlesByTitle('Article 1');
        self::assertEquals(2, $content['total']);

        $content = $this->getArticlesByTitle('Features');
        self::assertEquals(2, $content['total']);
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

    private function getArticlesByRouteId($routeId, $status = ArticleInterface::STATUS_PUBLISHED, $includeSubRoutes = false)
    {
        $client = static::createClient();

        $parameters = [
            'route' => $routeId,
            'status' => $status,
        ];
        if ($includeSubRoutes) {
            $parameters['includeSubRoutes'] = true;
        }

        $client->request('GET', $this->router->generate('swp_api_content_list_articles', $parameters));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function getArticlesByAuthor($author)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', ['author' => $author]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function getArticlesByPublicationDate($date, $type)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [$type => $date]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function getArticlesByTitle($query)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [
            'query' => $query,
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function getArticlesBySource($source)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles', [
            'source' => $source,
        ]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
