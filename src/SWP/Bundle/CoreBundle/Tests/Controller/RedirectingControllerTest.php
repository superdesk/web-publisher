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

class RedirectingControllerTest extends WebTestCase
{
    public function testRouteWithTrailingSlash()
    {
        $this->loadCustomFixtures(['tenant']);
        $client = static::createClient();
        $router = $this->getContainer()->get('router');
        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'news',
                'type' => 'content',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', '/news');
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/news/');
        self::assertEquals(301, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
