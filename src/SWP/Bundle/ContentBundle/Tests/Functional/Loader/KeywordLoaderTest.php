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

use SWP\Bundle\ContentBundle\Loader\KeywordLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class KeywordLoaderTest extends WebTestCase
{
    /**
     * @var KeywordLoader
     */
    protected $keywordLoader;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->databaseTool->loadFixtures(
            [
                LoadArticlesData::class,
            ]
        );

        $this->keywordLoader = new KeywordLoader(
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp.repository.keyword')
        );
    }

    public function testIfIsSupported(): void
    {
        $this->assertTrue($this->keywordLoader->isSupported('keyword'));
        $this->assertFalse($this->keywordLoader->isSupported('keywords'));
    }

    public function testLoadingSingleKeywordBySlug(): void
    {
        $keyword = $this->keywordLoader->load('keyword', ['slug' => 'big-city'], [], LoaderInterface::SINGLE);

        self::assertInstanceOf(Meta::class, $keyword);
        self::assertEquals('Big city', $keyword->name);
        self::assertEquals('big-city', $keyword->slug);

        $keyword = $this->keywordLoader->load('keyword', ['slug' => 'traffic'], [], LoaderInterface::SINGLE);

        self::assertInstanceOf(Meta::class, $keyword);
        self::assertEquals('traffic', $keyword->name);
        self::assertEquals('traffic', $keyword->slug);
    }
}
