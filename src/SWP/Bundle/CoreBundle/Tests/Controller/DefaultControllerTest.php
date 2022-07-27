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

class DefaultControllerTest extends WebTestCase
{
    protected static $devices = [
        'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5' => 'html:contains("theme_test/phone/index.html.twig")',
        'Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5' => 'html:contains("theme_test/tablet/index.html.twig")',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:8.0.1) Gecko/20100101 Firefox/8.0.1 FirePHP/0.6' => 'html:contains("theme_test/index.html.twig")',
        'no_agent_0' => 'html:contains("Homepage theme_test")',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'article']);
    }

    public function testIndexOnDevices()
    {
        $client = static::createClient();
        foreach (self::$devices as $userAgent => $filter) {
            if (!in_array($userAgent, ['no_agent_0', 'no_agent_1'])) {
                $client->setServerParameters([
                    'HTTP_USER_AGENT' => $userAgent,
                ]);
            }
            $crawler = $client->request('GET', '/');
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Wrong response code with device '.$userAgent);
            $this->assertGreaterThan(0, $crawler->filter($filter)->count(), 'Wrong filter');
        }
    }

    public function testHomepage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Homepage")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("id: 1")')->count());
    }
}
