<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Loader;

use SWP\Bundle\ContentBundle\Loader\ArticleMediaLoader;
use SWP\Bundle\ContentBundle\Loader\RelatedArticleLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class RelatedArticleLoaderTest extends WebTestCase
{
    /**
     * @var ArticleMediaLoader
     */
    protected $relatedArticleLoader;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->databaseTool->loadFixtures(
            [
                LoadArticlesData::class,
            ]
        );

        $this->relatedArticleLoader = new RelatedArticleLoader(
            $this->getContainer()->get('swp.repository.article'),
            $this->getContainer()->get('swp.repository.related_article'),
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp_template_engine_context')
        );
    }

    public function testIfIsSupported(): void
    {
        $this->assertTrue($this->relatedArticleLoader->isSupported('relatedArticles'));
    }

    public function testRelatedArticlesLoading(): void
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $relatedArticles = $this->relatedArticleLoader->load('relatedArticles', ['article' => $articleMeta], [], LoaderInterface::COLLECTION);

        self::assertCount(2, $relatedArticles);
        self::assertInstanceOf(Meta::class, $relatedArticles[0]);
        self::assertInstanceOf(Meta::class, $relatedArticles[1]);
        self::assertInstanceOf(Meta::class, $relatedArticles[0]->article);
        self::assertInstanceOf(Meta::class, $relatedArticles[1]->article);
        self::assertEquals('Features', $relatedArticles[0]->article->title);
        self::assertEquals('Test article', $relatedArticles[1]->article->title);
    }

    public function testRelatedArticlesLoadingWithoutArticleParameterProvided(): void
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleLoader->load('article', ['slug' => 'test-news-article']);

        $relatedArticles = $this->relatedArticleLoader->load('relatedArticles', [], [], LoaderInterface::COLLECTION);

        self::assertCount(2, $relatedArticles);
        self::assertInstanceOf(Meta::class, $relatedArticles[0]);
        self::assertInstanceOf(Meta::class, $relatedArticles[1]);
        self::assertInstanceOf(Meta::class, $relatedArticles[0]->article);
        self::assertInstanceOf(Meta::class, $relatedArticles[1]->article);
        self::assertEquals('Features', $relatedArticles[0]->article->title);
        self::assertEquals('Test article', $relatedArticles[1]->article->title);
    }
}
