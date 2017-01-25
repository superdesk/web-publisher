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

class FacebookInstantArticlesServiceTest extends \PHPUnit_Framework_TestCase
{
    private $facebookManager;

    private $facebookInstantArticlesArticleRepository;

    public function setUp()
    {
        $this->facebookManager = $this->getMock(FacebookManager::class);
        $this->facebookManager->method('createForApp')->willReturn($this->getMock(Facebook::class, [], [], '', false));

        $this->facebookInstantArticlesArticleRepository = $this->getMock(FacebookInstantArticlesArticleRepository::class, [], ['findInFeed'], '', false);
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
        $service = $this->getMock(FacebookInstantArticlesService::class, ['getClient', 'pushInstantArticle'], [
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->getMock(Factory::class, [], [], '', false),
            $this->facebookInstantArticlesArticleRepository,
        ]);
        $client = $this->getMock(Client::class, [], [], '', false);
        $client->method('importArticle')->willReturn('456345765423634563');
        $service->method('getClient')->willReturn($client);

        $feed = $this->getMock(FacebookInstantArticlesFeed::class);

        $service->pushInstantArticle(
            $feed,
            InstantArticle::create(),
            $this->getMock(Article::class)
        );
    }

    public function testUpdatingSubmission()
    {
        $service = $this->getMock(FacebookInstantArticlesService::class, ['getClient'], [
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->getMock(Factory::class, [], [], '', false),
            $this->facebookInstantArticlesArticleRepository,
        ]);
        $client = $this->getMock(Client::class, [], [], '', false);
        $client->method('getSubmissionStatus')->willReturn(new InstantArticleStatus('success'));
        $service->method('getClient')->willReturn($client);

        $result = $service->updateSubmissionStatus('2346345435632453246');

        self::assertInstanceOf(FacebookInstantArticlesArticle::class, $result);
    }

    private function getService()
    {
        return new FacebookInstantArticlesService(
            new FacebookInstantArticlesManager($this->facebookManager),
            $this->getMock(Factory::class, [], [], '', false),
            $this->facebookInstantArticlesArticleRepository
        );
    }
}
