<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class AmpHtmlTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'amp_html']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testLoadingAmpHtmlArticle()
    {
        $client = static::createClient();
        // default tenant
        $crawler = $client->request('GET', '/amp-articles/amp-html-article?amp');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $crawler->filter('amp-facebook')->count());
        self::assertEquals(1, $crawler->filter('html:contains("AMP Demo Theme")')->count());

        $client = static::createClient([], [
            'HTTP_HOST' => 'client1.'.$client->getContainer()->getParameter('env(SWP_DOMAIN)'),
            'HTTP_Authorization' => null,
        ]);

        // get amp page from another tenant
        $crawler = $client->request('GET', '/amp-articles-tenant-2/amp-html-article-tenant-2?amp');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $crawler->filter('amp-facebook')->count());
        self::assertEquals(1, $crawler->filter('html:contains("AMP Client1 Demo Theme")')->count());
    }

    public function testDisableEnableAmpSupport()
    {
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => '123abc',
        ]), [
            'ampEnabled' => false,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/amp-articles/amp-html-article?amp');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(0, $crawler->filter('amp-facebook')->count());
        self::assertEquals(1, $crawler->filter('html:contains("Content:")')->count());
        self::assertEquals(1, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());

        $client->request('PATCH', $this->router->generate('swp_api_core_update_tenant', [
            'code' => '123abc',
        ]), [
            'ampEnabled' => true,
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/amp-articles/amp-html-article?amp');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $crawler->filter('amp-facebook')->count());
    }

    public function testAmpHtmlOnRouteWithoutArticle()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/some-content?amp');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(0, $crawler->filter('amp-facebook')->count());
    }
}
