<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Tests\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use SWP\Bundle\CoreBundle\Adapter\AdapterInterface;
use SWP\Bundle\CoreBundle\Adapter\WordpressAdapter;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ExternalArticleInterface;
use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class WordpressAdapterTest extends WebTestCase
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
        $this->loadCustomFixtures(['tenant', 'article']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testPublishingArticleToOutputChannel()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_tenant'), [
            'tenant' => [
                'name' => 'Local Wordpress',
                'subdomain' => 'local_wordpress',
                'domainName' => 'localhost',
                'organization' => '123456',
                'outputChannel' => [
                    'type' => 'wordpress',
                    'config' => [
                        'url' => 'http://wordpress.test:8080',
                        'authorization_key' => 'Basic YWRtaW46dTJnWiB1QTlpIFVkYXogZnVtMSAxQnNkIHpwV2c=',
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        /** @var AdapterInterface $compositeOutputChannelAdapter */
        $compositeOutputChannelAdapter = $this->getContainer()->get('SWP\Bundle\CoreBundle\Adapter\CompositeOutputChannelAdapter');
        /** @var OutputChannelInterface $outputChannel */
        $outputChannel = $this->getContainer()->get('swp.repository.tenant')
            ->findOneBySubdomainAndDomain('local_wordpress', 'localhost')
            ->getOutputChannel();
        /** @var ArticleInterface $article */
        $article = $this->getContainer()->get('swp.repository.article')->findOneBy(['id' => 1]);

        $compositeOutputChannelAdapter->create($outputChannel, $article);
        $externalArticle = $article->getExternalArticle();
        self::assertInstanceOf(ExternalArticleInterface::class, $externalArticle);
        self::assertEquals(WordpressAdapter::STATUS_DRAFT, $externalArticle->getStatus());
        self::assertNotEmpty($externalArticle->getExternalId());
        self::assertNotEmpty($externalArticle->getLiveUrl());

        $guzzleClient = new Client();
        try {
            $response = $guzzleClient->request('GET', $externalArticle->getLiveUrl());
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
        self::assertEquals(404, $response->getStatusCode());
        self::assertNull($externalArticle->getPublishedAt());

        $compositeOutputChannelAdapter->publish($outputChannel, $article);
        $response = $guzzleClient->request('GET', $externalArticle->getLiveUrl());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(WordpressAdapter::STATUS_PUBLISHED, $externalArticle->getStatus());
        self::assertInstanceOf(\DateTime::class, $externalArticle->getPublishedAt());

        $previousUpdatedAt = $externalArticle->getUpdatedAt();
        $compositeOutputChannelAdapter->update($outputChannel, $article);
        self::assertNotEquals($previousUpdatedAt, $externalArticle->getUpdatedAt());

        $compositeOutputChannelAdapter->unpublish($outputChannel, $article);
        self::assertInstanceOf(\DateTime::class, $externalArticle->getUnpublishedAt());
        try {
            $response = $guzzleClient->request('GET', $externalArticle->getLiveUrl());
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
        self::assertEquals(404, $response->getStatusCode());
    }
}
