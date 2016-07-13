<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
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

    public function testLoadingArticleCustomTemplate()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/features');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'template_name' => 'test.html.twig',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(json_decode('{"id":"\/swp\/default\/content\/features","title":"Features","body":"Features content","slug":"features","status":"published","template_name":"test.html.twig","locale":"en","deleted_at":null,"children":null}', true), $responseArray);
        $this->assertTrue(null != $responseArray['updated_at']);
        $this->assertTrue($responseArray['updated_at'] >= $responseArray['created_at']);
    }

    public function testPublishingArticle()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        // unpublish article from fixtures
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'status' => 'new',
            ],
        ]);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(json_decode('{"status":"new"}', true), $responseArray);
        $crawler = $client->request('GET', '/articles/features');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        //publish unpublished article
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'features']), [
            'article' => [
                'status' => 'published',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(json_decode('{"id":"\/swp\/default\/content\/features","title":"Features","body":"Features content","slug":"features","status":"published","template_name":null,"locale":"en","deleted_at":null,"children":null}', true), $responseArray);
        $this->assertTrue(null != $responseArray['updated_at']);
        $this->assertTrue($responseArray['updated_at'] >= $responseArray['created_at']);
        $this->assertTrue($responseArray['published_at'] >= $responseArray['created_at']);

        $crawler = $client->request('GET', '/articles/features');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
    }
}
