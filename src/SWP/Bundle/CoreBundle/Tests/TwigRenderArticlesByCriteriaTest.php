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

namespace src\SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class TwigRenderArticlesByCriteriaTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'metadata_articles']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testRenderingArticlesByMetadata()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/news');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Articles by metadata: author Karen Ruhiger")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Article 2")')->count());
        self::assertEquals(0, $crawler->filter('html:contains("Article 1")')->count());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'templateName' => 'articles_by_metadata_v2.html.twig',
        ]);

        $crawler = $client->request('GET', '/news');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Articles by metadata: author Test Persona and located Sydney")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Article 1")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Article 2")')->count());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'templateName' => 'articles_by_metadata_v3.html.twig',
        ]);

        $crawler = $client->request('GET', '/news');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Articles by metadata: author Test Persona")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Article 1")')->count());
        self::assertEquals(0, $crawler->filter('html:contains("Article 2")')->count());
    }
}
