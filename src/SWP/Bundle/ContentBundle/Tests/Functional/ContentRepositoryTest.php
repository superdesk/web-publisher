<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;

class ContentRepositoryTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->initDatabase();
        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData',
            ], 'default'
        );
    }

    public function testFindNewArticle()
    {
        $repository = $this->getContainer()->get('swp.repository.article');
        $articles = $repository->findAllArticles();
        self::assertCount(8, $articles);
        $article1 = $repository->findOneBySlug('test-article');
        $this->assertInstanceOf($this->getContainer()->getParameter('swp.model.article.class'), $article1);

        $article2 = $repository->findOneBySlug('features-client1');
        $this->assertInstanceOf($this->getContainer()->getParameter('swp.model.article.class'), $article2);

        self::assertInstanceOf(ArticleInterface::class, $article1);
        self::assertInstanceOf(ArticleInterface::class, $article2);
    }
}
