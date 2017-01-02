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
            $this->getContainer()->get('swp_template_engine_context')
        );
    }

    public function testFindNewArticle()
    {
        $this->assertTrue($this->articleLoader->isSupported('article'));
        $this->assertTrue($this->articleLoader->isSupported('articles'));
        $this->assertFalse($this->articleLoader->isSupported('items'));

        $article = $this->articleLoader->load('article', ['slug' => 'test-article']);
        $this->assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $article);

        $this->assertNull($this->articleLoader->load('article', ['slug' => 'test-articles']));
        $this->assertNull($this->articleLoader->load('article', ['slug' => 'test-article'], LoaderInterface::COLLECTION));
        $this->assertTrue(count($this->articleLoader->load('articles', ['route' => '/news'], LoaderInterface::COLLECTION)) == 3);
        $this->assertNull($this->articleLoader->load('articles', ['route' => 99], LoaderInterface::COLLECTION));

        $this->assertNull($this->articleLoader->load('article', [], LoaderInterface::COLLECTION));
    }

    public function testLoadWithParameters()
    {
        $this->assertTrue(count($this->articleLoader->load('articles', ['route' => '/news', 'limit' => 2], LoaderInterface::COLLECTION)) == 2);

        $articlesCollection = $this->articleLoader->load('articles', ['route' => '/news', 'limit' => 1], LoaderInterface::COLLECTION);
        $this->assertTrue($articlesCollection->getTotalItemsCount() == 3);

        $articlesZero = $this->articleLoader->load('articles', ['route' => '/news'], LoaderInterface::COLLECTION);
        $articlesOne = $this->articleLoader->load('articles', ['route' => '/news', 'start' => 1], LoaderInterface::COLLECTION);
        $this->assertTrue($articlesZero[1]->title === $articlesOne[0]->title);

        $articlesAsc = $this->articleLoader->load('articles', ['route' => '/news', 'order' => ['title', 'asc']], LoaderInterface::COLLECTION);
        $articlesDesc = $this->articleLoader->load('articles', ['route' => '/news', 'order' => ['title', 'desc']], LoaderInterface::COLLECTION);

        $this->assertTrue(count($articlesAsc) == count($articlesDesc));

        $count = count($articlesAsc);
        $this->assertTrue($articlesAsc[0]->title === $articlesDesc[$count - 1]->title);
        $this->assertTrue($articlesAsc[$count - 1]->title === $articlesDesc[0]->title);
    }

    public function testLoadWithOrderByIdParameter()
    {
        $this->assertEquals(3, count($this->articleLoader->load('articles', ['route' => '/news', 'order' => ['id', 'asc']], LoaderInterface::COLLECTION)));
    }

    public function testLoadWithInvalidOrderByParameter()
    {
        $this->expectException(\Exception::class);

        $this->articleLoader->load('articles', ['route' => '/news', 'order' => ['truncate Table', 'asc']], LoaderInterface::COLLECTION);
    }
}
