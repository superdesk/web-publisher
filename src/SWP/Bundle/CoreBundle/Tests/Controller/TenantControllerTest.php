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

        $this->runCommand('swp:organization:create', ['--env' => 'test', '--default' => true], true);
        $this->runCommand('swp:tenant:create', ['--env' => 'test', '--default' => true], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateNewTenantWithDefaultOrganization()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"subdomain":"test","name":"Test Tenant","organization":{"id":"\/swp\/123456","name":"default","code":"123456"},"updated_at":null,"enabled":true,"theme_name":"swp\/test-theme"}', true
        ), json_decode(
            $client->getResponse()->getContent(),
            true
        ));
    }

    public function testCreateNewTenantWithCustomOrganization()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
                'organization' => '123456',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode(
            '{"subdomain":"test","name":"Test Tenant","organization":{"id":"\/swp\/123456","name":"default","code":"123456"},"updated_at":null,"enabled":true,"theme_name":"swp\/test-theme"}', true
        ), json_decode(
            $client->getResponse()->getContent(),
            true
        ));
    }

    public function testCreateNewTenantWhenProvidedThemeDoesntExist()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'fake/theme-name',
            ],
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testCreateNewTenantWhenItAlreadyExists()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTenant()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', $this->router->generate('swp_api_core_delete_tenant', [
            'code' => $content['code'],
        ]));

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testUpdateTenant()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Test Tenant',
                'subdomain' => 'test',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => $content['code'],
        ]), [
            'tenant' => [
                'name' => 'Updated tenant name',
                'subdomain' => 'updated test subdomain',
                'themeName' => 'swp/test-theme',
            ],
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertArraySubset(json_decode(
            '{"subdomain":"updated test subdomain","name":"Updated tenant name","organization":{"id":"\/swp\/123456","name":"default","code":"123456"},"enabled":true,"theme_name":"swp\/test-theme"}', true),
            json_decode($client->getResponse()->getContent(), true));
    }
}
