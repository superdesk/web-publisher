<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class OrganizationRuleControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

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

    public function testListOrganizationRulesApi()
    {
        $client = static::createClient();

        $this->createOrganizationRule();

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_rules'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v2\/organization\/rules\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/organization\/rules\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/organization\/rules\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"expression":"package.getLocated() matches \"\/Sydney\/\"","priority":1,"configuration":{"destinations":[{"tenant":"123abc"}]},"description":null,"name":null,"_links":{"self":{"href":"\/api\/v2\/rules\/1"}}}]}}';

        self::assertEquals($expected, $data);
    }

    public function testGetSingleOrganizationRuleApi()
    {
        $this->createOrganizationRule();

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_show_organization_rule', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"package.getLocated() matches \"\/Sydney\/\"","priority":1,"configuration":{"destinations":[{"tenant":"123abc"}]},"description":null,"name":null,"_links":{"self":{"href":"\/api\/v2\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testUpdateSingleOrganizationRuleApi()
    {
        $this->createOrganizationRule();

        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_organization_rule', ['id' => 1]), [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 11,
                'description' => 'desc',
                'name' => 'name',
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                            ],
                        ],
                    ],
                ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $expected = '{"id":1,"expression":"package.getLocated() matches \"\/Sydney\/\"","priority":11,"configuration":{"destinations":[{"tenant":"123abc"}]},"description":"desc","name":"name","_links":{"self":{"href":"\/api\/v2\/rules\/1"}}}';

        self::assertEquals($expected, $data);
    }

    public function testDeleteSingleOrganizationRuleApi()
    {
        $this->createOrganizationRule();
        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_core_delete_organization_rule', ['id' => 1]));

        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    private function createOrganizationRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                            ],
                        ],
                    ],
                ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
