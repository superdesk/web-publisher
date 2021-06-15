<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests;

use SWP\Bundle\ContentBundle\Loader\RouteLoader;
use SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

class RouteLoaderTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->loadFixtures([LoadArticlesData::class]);
    }

    public function testFindRoute()
    {
        $routeLoader = new RouteLoader(
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp.repository.route')
        );

        $meta = $routeLoader->load('route', ['route_object' => $this->getContainer()->get('swp.factory.route')->create()]);
        $this->assertNotNull($meta);

        $meta = $routeLoader->load('route', []);
        $this->assertFalse($meta);
    }

    public function testFindRoutesCollection()
    {
        $routeLoader = new RouteLoader(
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp.repository.route')
        );

        $collection = $routeLoader->load('route', [], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(MetaCollection::class, $collection);
        self::assertEquals(4, $collection->count());

        $collection = $routeLoader->load('route', ['parent' => 2], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(MetaCollection::class, $collection);
        self::assertEquals(1, $collection->count());

        $collection = $routeLoader->load('route', ['parent' => 3], [], LoaderInterface::COLLECTION);
        self::assertFalse($collection);

        $collection = $routeLoader->load('route', ['parent' => 'articles'], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(MetaCollection::class, $collection);
        self::assertEquals(1, $collection->count());

        $meta = $routeLoader->load('route', ['name' => 'articles']);
        $collection = $routeLoader->load('route', ['parent' => $meta], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(MetaCollection::class, $collection);
        self::assertEquals(1, $collection->count());
    }
}
