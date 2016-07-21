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
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;

class HttpCacheHeaderListenerTest extends WebTestCase
{
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
        $this->router = $this->getContainer()->get('router');
    }

    public function testNoCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(['name' => 'no-cache-route']);
        $this->assertFalse($headers->hasCacheControlDirective('max-age'));
    }

    public function testCacheRoute()
    {
        $headers = $this->getHeadersFromResponse(['name' => 'cache-route', 'cacheTimeInSeconds' => 1]);
        $this->assertTrue($headers->hasCacheControlDirective('public'));
        $this->assertEquals($headers->getCacheControlDirective('max-age'), 1);
        $this->assertEquals($headers->getCacheControlDirective('s-maxage'), 1);
    }

    private function getHeadersFromResponse($data)
    {
        $commonData = ['type' => 'content', 'parent' => '/', 'content' => null];
        $data = array_merge($data, $commonData);

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => $data,
        ]);

        $documentManager = $this->getContainer()->get('document_manager');
        $id = 'swp/default/routes'.$data['parent'].$data['name'];
        $route = $documentManager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', $id);

        $this->assertNotNull($route);
        $this->assertEquals($route->getName(), $data['name']);

        $client->request('GET', $this->router->generate($route));
        $response = $client->getResponse();
        $headers = $response->headers;

        return $headers;
    }
}
