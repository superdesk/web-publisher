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

namespace SWP\Bundle\ContentBundle\Tests\Loader;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Bundle\ContentBundle\Loader\ArticleLoader;
use SWP\Bundle\ContentBundle\Twig\Cache\CacheBlockTagsCollectorInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

class ArticleLoaderTest extends WebTestCase
{
    /**
     * @var ArticleLoader
     */
    protected $articleLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData',
            ], 'default'
        );

        $this->articleLoader = new ArticleLoader(
            $this->getContainer()->get('swp.provider.article'),
            $this->getContainer()->get('swp.provider.route'),
            $this->getContainer()->get('swp.object_manager.article'),
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp_template_engine_context'),
            $this->getContainer()->get(CacheBlockTagsCollectorInterface::class)
        );
    }

    public function testFindNewArticle()
    {
        $this->assertTrue($this->articleLoader->isSupported('article'));
        $this->assertTrue($this->articleLoader->isSupported('articles'));
        $this->assertFalse($this->articleLoader->isSupported('items'));

        $article = $this->articleLoader->load('article', ['slug' => 'test-article']);
        $this->assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $article);

        $this->assertFalse($this->articleLoader->load('article', ['slug' => 'test-articles']));
        $this->assertFalse($this->articleLoader->load('article', ['slug' => 'test-article'], [], LoaderInterface::COLLECTION));
        $this->assertTrue(3 == count($this->articleLoader->load('articles', ['route' => '/news'], [], LoaderInterface::COLLECTION)));
        $this->assertFalse($this->articleLoader->load('articles', ['route' => 99], [], LoaderInterface::COLLECTION));

        $this->assertFalse($this->articleLoader->load('article', [], [], LoaderInterface::COLLECTION));
    }

    public function testLoadWithParameters()
    {
        self::assertTrue(2 == count($this->articleLoader->load('articles', ['route' => '/news', 'limit' => 2], [], LoaderInterface::COLLECTION)));

        $articlesCollection = $this->articleLoader->load('articles', ['route' => '/news', 'limit' => 1], [], LoaderInterface::COLLECTION);
        self::assertTrue(3 === $articlesCollection->getTotalItemsCount());

        $articlesZero = $this->articleLoader->load('articles', ['route' => '/news'], [], LoaderInterface::COLLECTION);
        $articlesOne = $this->articleLoader->load('articles', ['route' => '/news', 'start' => 1], [], LoaderInterface::COLLECTION);
        self::assertTrue($articlesZero[1]->title === $articlesOne[0]->title);

        $articles = $this->articleLoader->load('articles', ['metadata' => ['located' => 'Sydney']], [], LoaderInterface::COLLECTION);
        self::assertCount(5, $articles);
        self::assertTrue(5 === $articles->getTotalItemsCount());

        $articles = $this->articleLoader->load('articles', ['extra' => ['video' => 'YES']], [], LoaderInterface::COLLECTION);
        self::assertCount(1, $articles);
        self::assertTrue(1 === $articles->getTotalItemsCount());

        $articles = $this->articleLoader->load('articles', ['extra' => ['rafal-embed' => [
            'embed' => 'embed link',
            'description' => "Shakin' Stevens"
        ]]], [], LoaderInterface::COLLECTION);
        self::assertCount(1, $articles);
        self::assertTrue(1 === $articles->getTotalItemsCount());

        $articles = $this->articleLoader->load('articles', ['metadata' => ['byline' => 'Jhon Doe']], [], LoaderInterface::COLLECTION);
        self::assertCount(5, $articles);
        self::assertTrue(5 === $articles->getTotalItemsCount());

        $articlesAsc = $this->articleLoader->load('articles', ['route' => '/news', 'order' => [['title', 'asc']]], [], LoaderInterface::COLLECTION);
        $articlesDesc = $this->articleLoader->load('articles', ['route' => '/news', 'order' => [['title', 'desc']]], [], LoaderInterface::COLLECTION);
        self::assertTrue(count($articlesAsc) == count($articlesDesc));

        $count = count($articlesAsc);
        self::assertTrue($articlesAsc[0]->title === $articlesDesc[$count - 1]->title);
        self::assertTrue($articlesAsc[$count - 1]->title === $articlesDesc[0]->title);
    }

    public function testLoadWithOrderByIdParameter()
    {
        $this->assertEquals(3, count($this->articleLoader->load('articles', ['route' => '/news', 'order' => [['id', 'asc']]], [], LoaderInterface::COLLECTION)));
    }

    public function testLoadWithInvalidOrderByParameter()
    {
        $articles = $this->articleLoader->load('articles', ['route' => '/news', 'order' => [['truncate Table', 'asc']]], [], LoaderInterface::COLLECTION);

        self::assertCount(3, $articles);
    }
}
