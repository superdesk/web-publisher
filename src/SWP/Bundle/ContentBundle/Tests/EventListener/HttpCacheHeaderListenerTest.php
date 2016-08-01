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

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Nelmio\Alice\Fixtures;
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
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->loadFixtures([
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
        $id = 'swp/123abc/routes/'.$name;
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
