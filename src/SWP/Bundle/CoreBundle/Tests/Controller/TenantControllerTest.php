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

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class TenantControllerTest extends WebTestCase
{
    const TEST_ITEM_ORIGIN = '{"body_html": "<p>this is test body</p><p>footer text</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:23:34+0000", "description_text": "test abstract", "byline": "Test Persona", "place": [], "version": "2", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99", "language": "en", "urgency": 3, "slugline": "test item update", "headline": "test headline", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:18:57+0000", "located": "Berlin", "type": "text", "description_html": "<p>test abstract</p>"}';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateNewTenantWithCustomOrganization()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
                'organization' => '123456',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"id":4,"subdomain":"test","name":"Test Tenant","organization":{"id":1,"name":"Organization1","code":"123456"},"enabled":true,"theme_name":"swp\/test-theme","domain_name":"localhost"}', true
        ), json_decode(
            $client->getResponse()->getContent(),
            true
        ));
    }

    public function testCreateNewTenantWhenProvidedThemeDoesntExist()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'fake/theme-name',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testCreateNewTenantWhenItAlreadyExists()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTenant()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test1',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', $this->router->generate('swp_api_core_delete_tenant', [
            'code' => $content['code'],
        ]));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient([], [
            'HTTP_HOST' => 'test.localhost',
        ]);
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_ORIGIN
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $routeContent = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                    'destinations' => [
                        [
                            'tenant' => $content['code'],
                            'route' => $routeContent['id'],
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                    ],
            ]
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_core_delete_tenant', [
            'code' => $content['code'],
        ]));
        $this->assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_core_delete_tenant', [
            'code' => $content['code'],
            'force' => true,
        ]));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testTenantWithWrongSubdomainAndRemoveIt()
    {
        $this->loadCustomFixtures(['tenant', 'article']);
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'google.com',
                'themeName' => 'swp/test-theme',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = \json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', $this->router->generate('swp_api_core_delete_tenant', [
            'code' => $content['code'],
        ]));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testUpdateTenant()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => $content['code'],
        ]), [
                'name' => 'Updated tenant name',
                'subdomain' => 'updated test subdomain',
                'themeName' => 'swp/test-theme',
                'domainName' => 'test.com',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"subdomain":"updated test subdomain","name":"Updated tenant name","organization":{"id":1,"name":"Organization1"},"enabled":true,"theme_name":"swp\/test-theme","domain_name":"test.com"}', true),
            json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => '123abc',
        ]), [
                'ampEnabled' => true,
                'fbiaEnabled' => true,
                'paywallEnabled' => true,
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"amp_enabled":true, "fbia_enabled": true, "paywall_enabled": true}', true),
            json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => '123abc',
        ]), [
                'paywallEnabled' => false,
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"amp_enabled":true, "fbia_enabled": true, "paywall_enabled": false}', true),
            json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => '123abc',
        ]), [
            'defaultLanguage' => 'pl',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"amp_enabled":true, "fbia_enabled": true, "paywall_enabled": false, "default_language": "pl"}', true),
            json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreateTwoNewTenantsWithCustomOrganization()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'domainName' => 'localhost',
                'themeName' => 'swp/test-theme',
                'organization' => '123456',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Second Tenant',
                'subdomain' => 'test2',
                'domainName' => 'localhost2',
                'themeName' => 'swp/test-theme',
                'organization' => '123456',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
                'name' => 'Test Third Tenant',
                'subdomain' => 'test3',
                'domainName' => 'localhost3',
                'themeName' => 'swp/test-theme',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testLoadNotExistingTenant()
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'notexisting.'.static::createClient()->getContainer()->getParameter('env(SWP_DOMAIN)'),
        ]);
        $client->request('GET', $this->router->generate('swp_api_core_get_tenant', ['code' => '123abc']));
        self::assertEquals(404, $client->getResponse()->getStatusCode());
        self::assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));

        $client->request('GET', '/');
        self::assertEquals(404, $client->getResponse()->getStatusCode());
        self::assertContains('Tenant for host "notexisting.localhost" could not be found!', $client->getResponse()->getContent());
        self::assertEquals('text/html; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
    }

    public function testCountingTenantArticles()
    {
        $this->loadCustomFixtures(['tenant', 'article']);

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_get_tenant', ['code' => '123abc']));
        $response = \json_decode($client->getResponse()->getContent(), true);

        self::assertSame(5, $response['articles_count']);

        $client->request('GET', $this->router->generate('swp_api_core_get_tenant', ['code' => '456def']));
        $response = \json_decode($client->getResponse()->getContent(), true);

        self::assertSame(0, $response['articles_count']);
    }
}
