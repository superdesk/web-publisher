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

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleMediaInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatisticsInterface;
use SWP\Bundle\CoreBundle\Theme\Generator\FakeArticlesGenerator;
use SWP\Bundle\CoreBundle\Theme\Generator\FakeArticlesGeneratorInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;

class FakeArticlesGeneratorTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
    }

    public function testFakeArticleGeneration()
    {
        $fakeArticlesGenerator = new FakeArticlesGenerator(
            $this->getContainer()->get('swp.factory.article'),
            $this->getContainer()->get('swp_content_bundle.manager.media'),
            $this->getContainer()->get('swp.factory.media'),
            $this->getContainer()->get('swp.repository.article'),
            $this->getContainer()->get('swp.factory.article_statistics')
        );

        self::assertInstanceOf(FakeArticlesGeneratorInterface::class, $fakeArticlesGenerator);
        self::assertCount(0, $fakeArticlesGenerator->generate(0));
        /** @var ArticleInterface[] $generatedArticles */
        $generatedArticles = $fakeArticlesGenerator->generate(5);
        self::assertCount(5, $generatedArticles);
        self::assertInstanceOf(ArticleInterface::class, $generatedArticles[0]);
        self::assertInstanceOf(ArticleStatisticsInterface::class, $generatedArticles[0]->getArticleStatistics());
        self::assertInstanceOf(ArticleMediaInterface::class, $generatedArticles[0]->getMedia()[0]);
    }
}
