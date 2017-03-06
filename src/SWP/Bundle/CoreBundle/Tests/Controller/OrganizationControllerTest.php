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

class OrganizationControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/organization_articles.yml',
        ], true);
    }

    public function testGetArticlesByOrganization()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(2, $content['total']);
        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"title":"art1","body":"art1 body","slug":"art1","publishedAt":null,"status":"new","route":null,"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":false,"metadata":[],"media":[],"featureMedia":null,"lead":"art1 lead","keywords":[],"code":"12356yjgktowud","_links":{"self":{"href":"\/api\/v1\/content\/articles\/art1"},"online":[{"href":"\/api\/v1\/content\/articles\/?slug=art1"},{"href":"\/api\/v1\/content\/articles\/"}]}},{"id":2,"title":"art2","body":"art2 body","slug":"art2","publishedAt":null,"status":"new","route":null,"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":false,"metadata":[],"media":[],"featureMedia":null,"lead":"art2 lead","keywords":[],"code":"87346yjgktowrt","_links":{"self":{"href":"\/api\/v1\/content\/articles\/art2"},"online":[{"href":"\/api\/v1\/content\/articles\/?slug=art2"},{"href":"\/api\/v1\/content\/articles\/"}]}}]}}', true), $content);
    }
}
