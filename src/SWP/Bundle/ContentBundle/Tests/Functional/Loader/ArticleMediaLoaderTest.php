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

use SWP\Bundle\ContentBundle\Loader\ArticleMediaLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class ArticleMediaLoaderTest extends WebTestCase
{
    /**
     * @var ArticleMediaLoader
     */
    protected $articleMediaLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->initDatabase();

        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');
        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesMediaData',
            ], 'default'
        );

        $this->articleMediaLoader = new ArticleMediaLoader(
            $this->getContainer()->get('swp.provider.media'),
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp_template_engine_context')
        );
    }

    /**
     * Check if Loader support correct types.
     */
    public function testIfIsSupported()
    {
        $this->assertTrue($this->articleMediaLoader->isSupported('articleMedia'));
        $this->assertFalse($this->articleMediaLoader->isSupported('articleMedias'));
    }

    /**
     * Load article and it's media by tested article media loader.
     */
    public function testArticleMediaLoading()
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $articleMedia = $this->articleMediaLoader->load('articleMedia', ['article' => $articleMeta]);

        self::assertCount(3, $articleMedia);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $articleMedia[0]);
        self::assertEquals('By Best Editor', $articleMedia[0]->byLine);
        self::assertCount(3, $articleMedia[0]->renditions);

        // test loading article media without article meta provided - it should use current article from context
        $articleMedia2 = $this->articleMediaLoader->load('articleMedia', []);
        self::assertEquals('By Best Editor', $articleMedia2[0]->byLine);
        self::assertCount(3, $articleMedia2[0]->renditions);
        self::assertEquals($articleMedia, $articleMedia2);
    }
}
