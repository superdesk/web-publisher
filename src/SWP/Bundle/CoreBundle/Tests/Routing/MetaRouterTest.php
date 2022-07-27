<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MetaRouterTest extends WebTestCase
{
    public function testSupportsMeta()
    {
        $article = $this->createMock('SWP\Bundle\ContentBundle\Model\ArticleInterface');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertTrue($router->supports(new Meta(new Context(new EventDispatcher(), new ArrayAdapter()), $article, ['name' => 'article', 'properties' => []])));
    }

    public function testSupports()
    {
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertTrue($router->supports('some_string'));
        $this->assertTrue($router->supports(new Article()));
    }

    public function testGenerate()
    {
        $this->loadCustomFixtures(['tenant', 'article']);

        $metaLoader = $this->getContainer()->get('swp_template_engine_loader_chain');
        $articleProvider = $this->getContainer()->get('swp.provider.article');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertEquals('/news/test-news-article', $router->generate($metaLoader->load('article', ['slug' => 'test-news-article'])));

        $criteria = new Criteria();
        $criteria->set('slug', 'test-news-article');
        $this->assertEquals('/news/test-news-article', $router->generate($articleProvider->getOneByCriteria($criteria)));
    }

    public function testGenerateForRouteWithContentWithoutRouteAssigned()
    {
        $this->loadCustomFixtures(['tenant', 'collection_route']);

        $metaLoader = $this->getContainer()->get('swp_template_engine_loader_chain');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $context = $this->getContainer()->get('swp_template_engine_context');
        $routeMeta = $metaLoader->load('route', ['route_object' => $this->getContainer()->get('swp.provider.route')->getRouteByName('collection-with-content')]);
        $context->setCurrentPage($routeMeta);
        $this->assertEquals(
            '/collection-with-content',
            $router->generate($metaLoader->load('article', ['slug' => 'content-assigned-as-route-content']))
        );
    }
}
