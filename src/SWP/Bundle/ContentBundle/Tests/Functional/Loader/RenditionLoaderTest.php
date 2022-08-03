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

use SWP\Bundle\ContentBundle\Loader\RenditionLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use Symfony\Component\Filesystem\Filesystem;

class RenditionLoaderTest extends WebTestCase
{
    /**
     * @var RenditionLoader
     */
    protected $renditionLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');
        $this->databaseTool->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesMediaData',
            ], 'default'
        );

        $this->renditionLoader = new RenditionLoader(
            $this->getContainer()->get('swp_template_engine_context'),
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory')
        );
    }

    /**
     * Check if Loader support correct types.
     */
    public function testIfIsSupported()
    {
        $this->assertTrue($this->renditionLoader->isSupported('rendition'));
        $this->assertFalse($this->renditionLoader->isSupported('renditions'));
        $this->assertFalse($this->renditionLoader->isSupported('articleMedia'));
    }

    public function testRenditionLoadingFromContextArticleMedia()
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $articleMediaLoader = $this->getContainer()->get('swp_template_engine.loader.media');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $articleMediaLoader->load('articleMedia', ['article' => $articleMeta]);
        $rendition = $this->renditionLoader->load('rendition', ['name' => '16-9']);

        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('16-9', $rendition->name);

        $rendition = $this->renditionLoader->load('rendition', ['name' => '160-90']);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('original', $rendition->name);

        $rendition = $this->renditionLoader->load('rendition', ['name' => '160-90', 'fallback' => '4-3']);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('4-3', $rendition->name);
    }

    public function testRenditionLoadingFromProvidedMeta()
    {
        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        /** @var MetaFactoryInterface $metaFactory */
        $metaFactory = $this->getContainer()->get('swp_template_engine_context.factory.meta_factory');
        $articleMeta = $articleLoader->load('article', ['slug' => 'test-news-article']);
        $articleMedia = $metaFactory->create($articleMeta->getValues()->getMedia()[0]);

        $removedId = $this->getContainer()->get('context')->temporaryUnset(['articleMedia']);

        $rendition = $this->renditionLoader->load('rendition', ['name' => '16-9', 'media' => $articleMedia]);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('16-9', $rendition->name);

        $rendition = $this->renditionLoader->load('rendition', ['name' => '160-90', 'media' => $articleMedia, 'fallback' => '4-3']);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('4-3', $rendition->name);

        $this->getContainer()->get('context')->restoreTemporaryUnset($removedId);

        $rendition = $this->renditionLoader->load('rendition', ['name' => '16-9', 'media' => null]);
        self::assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $rendition);
        self::assertEquals('16-9', $rendition->name);
    }
}
