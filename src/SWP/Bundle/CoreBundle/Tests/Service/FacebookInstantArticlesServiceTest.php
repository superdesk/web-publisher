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

namespace SWP\Bundle\CoreBundle\Tests\Service;

use Facebook\Facebook;
use Facebook\InstantArticles\Client\Client;
use Facebook\InstantArticles\Client\InstantArticleStatus;
use Facebook\InstantArticles\Elements\InstantArticle;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeed;
use SWP\Bundle\CoreBundle\Repository\FacebookInstantArticlesArticleRepository;
use SWP\Bundle\CoreBundle\Service\FacebookInstantArticlesService;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManager;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookManager;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Routing\Generator\UrlGenerator;

class FacebookInstantArticlesServiceTest extends \PHPUnit_Framework_TestCase
{
    private $facebookManager;

    private $facebookInstantArticlesArticleRepository;

    public function setUp()
    {
        $this->facebookManager = $this->createMock(FacebookManager::class);
        $this->facebookManager->method('createForApp')->willReturn($this->createMock(Facebook::class));
        $this->facebookInstantArticlesArticleRepository = $this->getMockBuilder(FacebookInstantArticlesArticleRepository::class)
            ->setConstructorArgs(['findInFeed'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->facebookInstantArticlesArticleRepository->method('findInFeed')->willReturn(new FacebookInstantArticlesArticle());
        $article = new FacebookInstantArticlesArticle();
        $article->setFeed(new FacebookInstantArticlesFeed());
        $this->facebookInstantArticlesArticleRepository->method('findSubmission')->willReturn($article);
    }

    public function testServiceInitialisation()
    {
        $service = $this->getService();

        self::assertInstanceOf(FacebookInstantArticlesService::class, $service);
    }

    public function testPushing()
    {
        $service = $this->getMockBuilder(FacebookInstantArticlesService::class)
            ->setConstructorArgs([
                new FacebookInstantArticlesManager($this->facebookManager),
                $this->createMock(Factory::class),
                $this->facebookInstantArticlesArticleRepository,
                $this->createMock(UrlGenerator::class),
            ])
            ->setMethods(['getClient', 'pushInstantArticle'])
            ->disableOriginalConstructor()
            ->getMock();
        $client = $this->createMock(Client::class);
        $client->method('importArticle')->willReturn('456345765423634563');
        $service->method('getClient')->willReturn($client);

        $feed = $this->createMock(FacebookInstantArticlesFeed::class);

        $service->pushInstantArticle(
            $feed,
            InstantArticle::create(),
            $this->createMock(Article::class)
        );
    }

    public function testUpdatingSubmission()
    {
        $service = $this->getMockBuilder(FacebookInstantArticlesService::class)
        ->setMethods(['getClient'])
        ->setConstructorArgs([
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->createMock(Factory::class),
            $this->facebookInstantArticlesArticleRepository,
            $this->createMock(UrlGenerator::class),
        ])
        ->getMock();
        $client = $this->createMock(Client::class);
        $client->method('getSubmissionStatus')->willReturn(new InstantArticleStatus('success'));
        $service->method('getClient')->willReturn($client);

        $result = $service->updateSubmissionStatus('2346345435632453246');

        self::assertInstanceOf(FacebookInstantArticlesArticle::class, $result);
    }

    public function testRemoving()
    {
        $service = $this->getMockBuilder(FacebookInstantArticlesService::class)
        ->setMethods(['getClient', 'pushInstantArticle'])
        ->setConstructorArgs([
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->createMock(Factory::class, [], [], '', false),
            $this->facebookInstantArticlesArticleRepository,
            $this->createMock(UrlGenerator::class, [], [], '', false),
        ])
        ->getMock();
        $client = $this->createMock(Client::class);

        $client->method('removeArticle')->willReturn($this->createMock(InstantArticleStatus::class));
        $service->method('getClient')->willReturn($client);
        $feed = $this->createMock(FacebookInstantArticlesFeed::class);

        $service->removeInstantArticle(
            $feed,
            $this->createMock(Article::class)
        );
    }

    private function getService()
    {
        return new FacebookInstantArticlesService(
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->createMock(Factory::class),
            $this->facebookInstantArticlesArticleRepository,
            $this->createMock(UrlGenerator::class)
        );
    }
}
