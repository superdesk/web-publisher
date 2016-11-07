<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
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
        self::bootKernel();

        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article']);

        $this->runCommand('theme:setup', ['--env' => 'test'], true);
        $this->router = $this->getContainer()->get('router');
    }

    public function testLoadingArticlesCollection()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('GET', $this->router->generate('swp_api_content_list_articles'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('self', $responseArray['_embedded']['_items'][0]['_links']);
        self::assertArrayHasKey('online', $responseArray['_embedded']['_items'][0]['_links']);
    }

    public function testLoadingArticleCustomTemplate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/news/features');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);

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
        $client->request('GET', '/news/features');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

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

        $crawler = $client->request('GET', '/news/features');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
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
        $client->enableProfiler();
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'route' => 3,
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
        self::assertArraySubset(['route' => ['id' => 3]], json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'test-news-article']), [
            'article' => [
                'route' => 4,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 4, 'name' => 'articles']],
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
