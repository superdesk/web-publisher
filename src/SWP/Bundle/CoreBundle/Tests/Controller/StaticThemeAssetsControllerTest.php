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

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;

class StaticThemeAssetsControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
    }

    public function testFetchingServiceWorkerFiles()
    {
        $client = static::createClient();
        $client->request('GET', '/sw.js');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/sw.html');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/public-robots.txt');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/public/robots.txt');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/sw2.js');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testFetchingFilesFromPublicDirectory()
    {
        $client = static::createClient();
        $client->request('GET', '/public/sw.js');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/javascript', $client->getResponse()->headers->get('Content-Type'));
        $client->request('GET', '/public/css/test.css');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('text/css; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));

        $client->request('GET', '/public/noneexisitng.js');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
