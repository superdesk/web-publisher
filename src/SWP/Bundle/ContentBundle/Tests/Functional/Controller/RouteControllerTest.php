<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Functional\Controller;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RouteControllerTest extends WebTestCase
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
        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateEmptyContentRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertEquals(json_decode('{"id":1,"content":null,"staticPrefix":"\/simple-test-route","variablePattern":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"content","cacheTimeInSeconds":0,"name":"simple-test-route","position":0,"root":1,"parent":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}, "slug":null}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreateContentRoutesApi()
    {
        $this->loadFixtureFiles(
            ['@SWPContentBundle/Tests/Functional/app/Resources/fixtures/separate_article.yml'],
            'default'
        );

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => 2,
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArraySubset(json_decode('{"id":2,"content":{"id":2,"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"id":1,"content":null,"staticPrefix":null,"variablePattern":"\/{slug}","children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"news","position":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}},"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":true,"metadata":null,"media":[],"lead":null,"keywords":[],"_links":{"self":{"href":"\/api\/v1\/content\/articles\/test-content-article"},"online":{"href":"\/test-content-article"}}},"staticPrefix":"\/simple-test-route","variablePattern":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"content","cacheTimeInSeconds":1,"name":"simple-test-route","position":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/2"}}}', true), $content);
    }

    public function testCreateAndUpdateRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":1,"content":null,"staticPrefix":"\/simple-test-route","variablePattern":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"content","cacheTimeInSeconds":1,"name":"simple-test-route","position":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 1]), [
            'route' => [
                'name' => 'simple-test-route-new-name',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['name' => 'simple-test-route-new-name'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testCreateAndUpdateAndDeleteRoutesApi()
    {
        $this->loadFixtureFiles(
            [
                '@SWPContentBundle/Tests/Functional/app/Resources/fixtures/separate_article.yml',
            ], 'default'
        );

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]), [
            'route' => [
                'name' => 'simple-edited-test-route',
                'type' => 'collection',
                'content' => 2,
                'cacheTimeInSeconds' => 50,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"content":{"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"content":null,"staticPrefix":null,"variablePattern":"\/{slug}","children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"news","position":0},"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":true,"metadata":null,"media":[],"lead":null},"staticPrefix":"\/simple-edited-test-route","variablePattern":"\/{slug}","children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":50,"name":"simple-edited-test-route","position":1}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]), [
            'route' => [
                'content' => null,
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testWithCustomTemplatesRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"content":null,"staticPrefix":"\/simple-test-route","variablePattern":null,"children":[],"level":0,"templateName":"test.html.twig","articlesTemplateName":null,"type":"content","cacheTimeInSeconds":1,"name":"simple-test-route","position":0}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testSettingNotSupportedRouteType()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'testing-route-type',
                'type' => 'fake-type',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"type":{"errors":["The type \"fake-type\" is not allowed. Supported types are: \"collection, content\"."]}}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testNestedRoutes()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root',
                'type' => 'collection',
            ],
        ]);

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root-child1',
                'type' => 'collection',
                'parent' => $rootContent['id'],
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'child1-root-child1',
                'type' => 'collection',
                'parent' => $content['id'],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', ['id' => $rootContent['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertArraySubset(json_decode('{"id":1,"content":null,"staticPrefix":"\/root","variablePattern":"\/{slug}","children":[{"id":2,"content":null,"staticPrefix":"\/root\/root-child1","variablePattern":"\/{slug}","children":[{"id":3,"content":null,"staticPrefix":"\/root\/root-child1\/child1-root-child1","variablePattern":"\/{slug}","children":[],"level":2,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"child1-root-child1","position":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/3"},"parent":{"href":"\/api\/v1\/content\/routes\/2"},"root":{"href":"\/api\/v1\/content\/routes\/1"}}}],"level":1,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"root-child1","position":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/2"},"parent":{"href":"\/api\/v1\/content\/routes\/1"},"root":{"href":"\/api\/v1\/content\/routes\/1"}}}],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"root","position":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', true), $content);
    }

    public function testAssigningNotExistingRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root',
                'type' => 'collection',
                'parent' => 99999,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"message":"Validation Failed","errors":{"children":{"parent":{"errors":["The selected route does not exist!"]}}}}', true), $content);
    }

    public function testFilterRoutesByType()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'route1',
                'type' => 'content',
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'route2',
                'type' => 'collection',
                'cacheTimeInSeconds' => 2,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_create_routes'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"content":null,"staticPrefix":"\/route1","variablePattern":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"content","cacheTimeInSeconds":1,"name":"route1","position":0,"root":1,"parent":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}},"slug":null},{"id":2,"content":null,"staticPrefix":"\/route2","variablePattern":"\/{slug}","children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":2,"name":"route2","position":1,"root":2,"parent":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/2"}},"slug":null}]}}', true), $content);

        $client->request('GET', $this->router->generate('swp_api_content_create_routes', [
            'type' => 'content',
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(json_decode('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"content":null,"staticPrefix":"\/route1","variablePattern":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"content","cacheTimeInSeconds":1,"name":"route1","position":0,"root":1,"parent":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}},"slug":null}]}}', true), $content);

        $client->request('GET', $this->router->generate('swp_api_content_create_routes', [
            'type' => 'collection',
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(json_decode('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":2,"content":null,"staticPrefix":"\/route2","variablePattern":"\/{slug}","children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":2,"name":"route2","position":1,"root":2,"parent":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/2"}},"slug":null}]}}', true), $content);

        $client->request('GET', $this->router->generate('swp_api_content_create_routes', [
            'type' => 'fake',
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(json_decode('{"page":1,"limit":10,"pages":1,"total":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[]}}', true), $content);
    }
}
