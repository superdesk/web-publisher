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

use SWP\Bundle\ContentBundle\Tests\Functional\Controller\ContentPushControllerTest;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RuleControllerTest extends WebTestCase
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
        $this->loadCustomFixtures(['tenant', 'rule']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testListRulesApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_rule'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/rules\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/rules\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/rules\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":1,"configuration":{"route":3},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}]}}';

        self::assertEquals($expected, $data);
    }

    public function testGetSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_get_rule', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":1,"configuration":{"route":3},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testUpdateSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_rule', [
            'id' => 1,
        ]), [
            'rule' => [
                'priority' => 22,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'test.html.twig',
                    ],
                ],
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":22,"configuration":{"templateName":"test.html.twig"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testUpdateSingleRuleWithMoreConfigurationApi()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_rule', [
            'id' => 1,
        ]), [
            'rule' => [
                'priority' => 22,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'test.html.twig',
                    ],
                    [
                        'key' => 'route',
                        'value' => 3,
                    ],
                ],
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"article.getLocale() matches \"\/en\/\"","priority":22,"configuration":{"templateName":"test.html.twig","route":"3"},"_links":{"self":{"href":"\/api\/v1\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testDeleteSingleRuleApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_core_delete_rule', ['id' => 1]));

        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testCreateNewRuleApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 2,
                'configuration' => [
                    [
                        'key' => 'templateName',
                        'value' => 'sydney.html.twig',
                    ],
                    [
                        'key' => 'route',
                        'value' => 3,
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":2,"expression":"article.getMetadataByKey(\"located\") matches \"\/Sydney\/\"","priority":2,"configuration":{"templateName":"sydney.html.twig","route":"3"},"_links":{"self":{"href":"\/api\/v1\/rules\/2"}}}';

        self::assertEquals($expected, $data);
    }

    public function testAssigningArticleRouteOnContentPushBasedOnRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Porto/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'route',
                        'value' => 4,
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles/assign-article-automatically-here',
                'type' => 'content',
                'content' => null,
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(0, $data['articlesCount']);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ContentPushControllerTest::TEST_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->publishArticle();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $data['route']['articlesCount']);
        self::assertArraySubset(
            ['route' => ['name' => 'articles/assign-article-automatically-here']],
            $data
        );
    }

    public function testNotAssigningRouteIfRuleNotMatch()
    {
        $this->loadCustomFixtures(['tenant']);
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Fake/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'route',
                        'value' => 2,
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ContentPushControllerTest::TEST_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->publishArticle();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['id' => 3]],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testAssigningRouteAndTemplateNameBasedOnRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Porto/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'route',
                        'value' => 4,
                    ],
                    [
                        'key' => 'templateName',
                        'value' => 'test.html.twig',
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles/assign-article-automatically-here',
                'type' => 'content',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ContentPushControllerTest::TEST_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->publishArticle();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['route' => ['name' => 'articles/assign-article-automatically-here']],
            json_decode($client->getResponse()->getContent(), true)
        );

        self::assertArraySubset(
            ['templateName' => 'test.html.twig'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    private function publishArticle()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
