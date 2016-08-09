<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests\EventListener;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData;
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
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData',
        ], null, 'doctrine_phpcr');

        $this->router = $this->getContainer()->get('router');
    }

    public function testNoCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(LoadRoutesData::TEST_NO_CACHE_ROUTE_NAME);
        $this->assertFalse($headers->hasCacheControlDirective('max-age'));
    }

    public function testCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(LoadRoutesData::TEST_CACHE_ROUTE_NAME);
        $this->assertTrue($headers->hasCacheControlDirective('public'));
        $this->assertEquals($headers->getCacheControlDirective('max-age'), LoadRoutesData::TEST_CACHE_TIME);
        $this->assertEquals($headers->getCacheControlDirective('s-maxage'), LoadRoutesData::TEST_CACHE_TIME);
    }

    private function getHeadersFromResponse($name)
    {
        $documentManager = $this->getContainer()->get('document_manager');
        $id = 'swp/123456/123abc/routes/'.$name;
        $route = $documentManager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', $id);

        $this->assertNotNull($route);
        $this->assertEquals($route->getName(), $name);

        $client = static::createClient();
        $client->request('GET', $this->router->generate($route));
        $response = $client->getResponse();
        $headers = $response->headers;

        return $headers;
    }
}
