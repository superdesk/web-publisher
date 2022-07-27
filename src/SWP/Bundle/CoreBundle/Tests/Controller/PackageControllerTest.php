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
use SWP\Component\Bridge\Model\ContentInterface;
use Symfony\Component\Routing\RouterInterface;

final class PackageControllerTest extends WebTestCase
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
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'package', 'route']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testListAllPackagesApi()
    {
        $this->runCommand('fos:elastica:populate', ['--env' => 'test'], true);
        sleep(2);
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_packages'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(2, $content['total']);
        self::assertEquals('new', $content['_embedded']['_items'][0]['status']);
        self::assertEmpty($content['_embedded']['_items'][0]['articles']);
    }

    public function testFilterByPackageStatusApi()
    {
        $this->runCommand('fos:elastica:populate', ['--env' => 'test'], true);
        sleep(2);
        $content = $this->filterByStatus('new');
        self::assertEquals(2, $content['total']);

        $this->publishPackage();
        // wait 1 second so it can index package
        // before getting it
        sleep(1);

        $content = $this->filterByStatus('published');

        self::assertEquals(1, $content['total']);

        $this->unpublishPackage();
        sleep(1);

        $content = $this->filterByStatus('unpublished');

        self::assertEquals(1, $content['total']);

        $content = $this->filterByStatus('canceled');

        self::assertEquals(0, $content['total']);
    }

    private function filterByStatus(string $status)
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_list_packages', ['status' => [$status]]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    public function testGetSinglePackagesApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('new', $content['status']);
        self::assertEmpty($content['articles']);
    }

    public function testPublishPackageApi()
    {
        $client = static::createClient();
        $this->publishPackage();

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $content['status']);
        self::assertCount(1, $content['articles']);
        self::assertEquals('published', $content['articles'][0]['status']);
        self::assertEquals($content['headline'], $content['articles'][0]['title']);
        self::assertEquals(3, $content['articles'][0]['route']['id']);
    }

    public function testRouteChangeWhenPackageAlreadyPublishedUnderExistingRoute()
    {
        $client = static::createClient();
        $this->publishPackage();

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $content['status']);
        self::assertCount(1, $content['articles']);
        self::assertEquals('published', $content['articles'][0]['status']);
        self::assertEquals($content['headline'], $content['articles'][0]['title']);
        self::assertEquals(3, $content['articles'][0]['route']['id']);

        $this->publishPackage(4);

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $content['status']);
        self::assertCount(1, $content['articles']);
        self::assertEquals('published', $content['articles'][0]['status']);
        self::assertEquals($content['headline'], $content['articles'][0]['title']);
        self::assertEquals(4, $content['articles'][0]['route']['id']);
    }

    public function testUnpublishPackageApi()
    {
        $client = static::createClient();
        $this->publishPackage();

        $this->unpublishPackage();

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('unpublished', $content['status']);
        self::assertCount(1, $content['articles']);
        self::assertEquals('unpublished', $content['articles'][0]['status']);
        self::assertEquals($content['headline'], $content['articles'][0]['title']);
    }

    public function testCancelPackageApi()
    {
        $client = static::createClient();
        $client->request(
            'PATCH',
            $this->router->generate('swp_api_core_update_package', ['id' => 1]),
            [
                    'pubStatus' => ContentInterface::STATUS_CANCELED,
            ]
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_show_package', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(ContentInterface::STATUS_CANCELED, $content['status']);
    }

    private function publishPackage(int $routeId = 3)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]),
            [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => $routeId,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                    ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
    }

    private function unpublishPackage()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_unpublish_package', ['id' => 1]),
            [
                    'tenants' => ['123abc'],
            ]
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
