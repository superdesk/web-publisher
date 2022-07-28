<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Loader;

use SWP\Bundle\ContentBundle\Loader\SlideshowLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesSlideshowsData;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class SlideshowLoaderTest extends WebTestCase
{
    /**
     * @var SlideshowLoader
     */
    protected $slideshowLoader;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->databaseTool->loadFixtures(
            [
                LoadArticlesSlideshowsData::class,
            ]
        );

        $this->slideshowLoader = new SlideshowLoader(
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp.repository.slideshow'),
            $this->getContainer()->get('swp_template_engine_context')
        );
    }

    public function testIfIsSupported(): void
    {
        $this->assertTrue($this->slideshowLoader->isSupported('slideshow'));
        $this->assertTrue($this->slideshowLoader->isSupported('slideshows'));
    }

    public function testLoadingSlideshows(): void
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $slideshows = $this->slideshowLoader->load('slideshows', ['article' => $articleMeta], [], LoaderInterface::COLLECTION);

        self::assertCount(2, $slideshows);
        self::assertInstanceOf(Meta::class, $slideshows[0]);
        self::assertEquals('slideshow1', $slideshows[0]->code);
        self::assertEquals('slideshow3', $slideshows[1]->code);

        $slideshows2 = $this->slideshowLoader->load('slideshows', [], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(Meta::class, $slideshows2[0]);
        self::assertEquals('slideshow1', $slideshows2[0]->code);
        self::assertEquals('slideshow3', $slideshows2[1]->code);
        self::assertEquals($slideshows[0]->code, $slideshows2[0]->code);
    }

    public function testLoadingSingleSlideshowByName(): void
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $slideshow = $this->slideshowLoader->load('slideshow', ['article' => $articleMeta, 'name' => 'slideshow1'], [], LoaderInterface::SINGLE);

        self::assertInstanceOf(Meta::class, $slideshow);
        self::assertEquals('slideshow1', $slideshow->code);

        $slideshow2 = $this->slideshowLoader->load('slideshow', ['name' => 'slideshow1'], [], LoaderInterface::SINGLE);
        self::assertInstanceOf(Meta::class, $slideshow2);
        self::assertEquals('slideshow1', $slideshow2->code);
        self::assertEquals($slideshow->code, $slideshow2->code);

        $slideshow = $this->slideshowLoader->load('slideshow', ['article' => $articleMeta, 'name' => 'slideshow3'], [], LoaderInterface::SINGLE);

        self::assertInstanceOf(Meta::class, $slideshow);
        self::assertEquals('slideshow3', $slideshow->code);

        // try to load slideshow from different article
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article-2']);
        $slideshow = $this->slideshowLoader->load('slideshow', ['article' => $articleMeta, 'name' => 'slideshow1'], [], LoaderInterface::SINGLE);

        self::assertNotInstanceOf(Meta::class, $slideshow);
        self::assertFalse($slideshow);
    }
}
