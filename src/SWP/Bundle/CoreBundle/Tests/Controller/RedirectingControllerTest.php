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
    public function testRedirectBasedOnExtraData()
    {
        $this->loadCustomFixtures(['tenant', 'article']);
        $client = static::createClient();

        $client->request('GET', '/redirecting/extra/articleNumber/10242');
        self::assertEquals(301, $client->getResponse()->getStatusCode());
        self::assertEquals('http://localhost/news/test-article', $client->getResponse()->headers->get('Location'));

        $client->request('GET', '/redirecting/extra/webcode/+jxux6');
        self::assertEquals(301, $client->getResponse()->getStatusCode());
        self::assertEquals('http://localhost/news/sports/test-news-sports-article', $client->getResponse()->headers->get('Location'));

        $client->request('GET', '/redirecting/extra/articleNumber/1919');
        self::assertEquals(301, $client->getResponse()->getStatusCode());
        self::assertEquals('http://localhost/news/sports/test-news-sports-article', $client->getResponse()->headers->get('Location'));

        $client->request('GET', '/redirecting/extra/articleNumber/10242?amp');
        self::assertEquals(301, $client->getResponse()->getStatusCode());
        self::assertEquals('http://localhost/news/test-article?amp=1', $client->getResponse()->headers->get('Location'));
    }
}
