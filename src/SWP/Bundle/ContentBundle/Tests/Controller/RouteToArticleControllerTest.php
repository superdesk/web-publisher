<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests\Controller;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\ContentBundle\Validator\Constraints\RouteId;

class RouteToArticleControllerTest extends WebTestCase
{
    protected $router;

    protected $defaultData;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/RouteToArticle.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');

        $this->defaultData = [
            'id' => 1,
            'rule' => 'article.getLocale() matches "/en/"',
            'priority' => 1,
            'routeId' => 'articles/features',
        ];
    }

    public function testListRouteToArticleApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_mapping_list_route_to_article'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['page'], 1);

        $item = $data['_embedded']['_items'][0];
        $this->checkResponsePayload($client, $this->defaultData, $item);
    }

    public function testGetRouteToArticleApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_mapping_get_route_to_article', ['id' => 1]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->checkResponsePayload($client, $this->defaultData);
    }

    public function testCreateRouteToArticleApi()
    {
        $sent = [
            'rule' => 'article.getLocale() matches "/test/"',
            'priority' => 0,
            'routeId' => 'articles/features',
            'templateName' => 'blumenkohl',
        ];

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_mapping_create_route_to_article'), [
            'routetoarticle' => $sent,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->checkResponsePayload($client, $sent);
    }

    public function testUpdateRouteToArticleApi()
    {
        $sent = [
            'rule' => 'article.getLocale() matches "/jest/"',
            'priority' => 1,
            'routeId' => LoadRoutesData::TEST_NO_CACHE_ROUTE_NAME,
            'templateName' => 'iridify',
        ];

        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_mapping_update_route_to_article', ['id' => 1]), [
            'routetoarticle' => $sent,
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->checkResponsePayload($client, $sent);
    }

    public function testInvalidRouteToArticleRouteIdApi()
    {
        $sent = [
            'routeId' => 'invalid route',
        ];

        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_mapping_update_route_to_article', ['id' => 1]), [
            'routetoarticle' => $sent,
        ]);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $constraint = new RouteId();
        $message = str_replace('%string%', $sent['routeId'], $constraint->message);
        $this->assertContains($message, $client->getResponse()->getContent());
    }

    public function testInvalidRouteToArticleRuleApi()
    {
        $sent = [
            'rule' => 'lkjfalj',
        ];

        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_mapping_update_route_to_article', ['id' => 1]), [
            'routetoarticle' => $sent,
        ]);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testDeleteRouteToArticleApi()
    {
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_mapping_delete_route_to_article', ['id' => 1]));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getResponse()->getContent(), '');
    }

    private function checkResponsePayload($client, $sent, $data = null)
    {
        $naming = new CamelCaseNamingStrategy();
        if (null === $data) {
            $data = $client->getResponse()->getContent();
            $data = json_decode($data, true);
        }
        foreach ($sent as $key => $value) {
            $metaData = new PropertyMetadata('SWP\Bundle\ContentBundle\Model\RouteToArticle', $key);
            $serializedKey = $naming->translateName($metaData);
            $this->assertEquals($data[$serializedKey], $sent[$key]);
        }
    }
}
