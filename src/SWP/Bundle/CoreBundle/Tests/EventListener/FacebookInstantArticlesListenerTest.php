<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\EventListener;

use SWP\Bundle\ContentBundle\Tests\Functional\Controller\ContentPushControllerTest;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Cmf\Component\Routing\ChainRouter;

class FacebookInstantArticlesListenerTest extends WebTestCase
{
    /**
     * @var ChainRouter
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testPushingArticleToFBIA()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
        ]);

        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
                'expression' => 'article.getLocale() == "en"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'published',
                        'value' => true,
                    ],
                    [
                        'key' => 'route',
                        'value' => 3,
                    ],
                ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
                'name' => 'Example bucket',
                'type' => 'bucket',
                'description' => 'New FBIA list',
                'limit' => 0,
                'cacheLifeTime' => 0,
                'filters' => '{"metadata":{"language":"en"}}',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_instant_articles_feed'), [
                'contentBucket' => 1,
                'facebookPage' => 1,
                'mode' => 0,
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

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'isPublishedFbia' => true,
                            'published' => true,
                        ],
                    ],
            ]
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Page is not authorized to publish Instant Articles', $response['message']);
    }
}
