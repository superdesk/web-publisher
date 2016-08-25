<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\Loader\RouteLoader;

class RouteLoaderTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testFindRoute()
    {
        $routeLoader = new RouteLoader(
            $this->getContainer()->getParameter('kernel.root_dir'),
            $this->getContainer()->get('doctrine_cache.providers.main_cache')
        );

        $meta = $routeLoader->load('route', ['route_object' => new Route()]);
        $this->assertNotNull($meta);

        $meta = $routeLoader->load('route', []);
        $this->assertFalse($meta);
    }
}
