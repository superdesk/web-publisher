<?php

/**
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
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class MetaRouterTest extends WebTestCase
{
    public function testSupportsMeta()
    {
        $article = $this->getMock('SWP\Bundle\ContentBundle\Model\ArticleInterface');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertTrue($router->supports(new Meta(new Context(new ArrayCache()), $article, ['name' => 'article', 'properties' => []])));
    }

    public function testSupports()
    {
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertTrue($router->supports('some_string'));
    }

    public function testGenerate()
    {
        self::bootKernel();

        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $metaLoader = $this->getContainer()->get('swp_template_engine_loader_chain');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertEquals(
            '/news/test-news-article',
            $router->generate($metaLoader->load('article', ['contentPath' => '/swp/123456/123abc/content/test-news-article']))
        );
    }

    public function testGenerateForRouteWithContentWithoutRouteAssigned()
    {
        self::bootKernel();

        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $metaLoader = $this->getContainer()->get('swp_template_engine_loader_chain');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $context = $this->getContainer()->get('swp_template_engine_context');
        $routeMeta = $metaLoader->load('route', ['route_object' => $this->getContainer()->get('swp.provider.route')->getOneById('collection-with-content')]);
        $context->setCurrentPage($routeMeta);

        $this->assertEquals(
            '/collection-with-content',
            $router->generate($metaLoader->load('article', ['contentPath' => '/swp/123456/123abc/content/content-assigned-as-route-content']))
        );
    }
}
