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

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRoutesData;
use Symfony\Cmf\Component\Routing\ChainRouter;

class HttpCacheHeaderListenerTest extends WebTestCase
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
        $this->databaseTool->loadFixtures(
            [
                'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
                'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRoutesData',
            ], false
        );

        $this->router = $this->getContainer()->get('router');
    }

    public function testNoCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(LoadRoutesData::TEST_NO_CACHE_ROUTE_NAME);
        self::assertFalse($headers->hasCacheControlDirective('max-age'));
    }

    public function testCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(LoadRoutesData::TEST_CACHE_ROUTE_NAME);
        self::assertTrue($headers->hasCacheControlDirective('public'));
        self::assertEquals($headers->getCacheControlDirective('s-maxage'), LoadRoutesData::TEST_CACHE_TIME);
    }

    private function getHeadersFromResponse($name)
    {
        $routeProvider = $this->getContainer()->get('swp.provider.route');
        $route = $routeProvider->getRouteByName($name);
        self::assertNotNull($route);
        self::assertEquals($route->getName(), $name);

        $client = static::createClient([], [
            'HTTP_Authorization' => null,
        ]);
        $client->request('GET', $this->router->generate($route));
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode());

        return $response->headers;
    }
}
