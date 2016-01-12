<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\WebRendererBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\TemplatesSystem\Gimme\Meta\Meta;

class MetaRouterTest extends WebTestCase
{
    public function testSupportsMeta()
    {
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertTrue($router->supports(new Meta(['properties' => []], false)));
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
            'SWP\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $metaLoader = $this->getContainer()->get('swp_template_engine_loader_chain');
        $router = $this->getContainer()->get('cmf_routing.dynamic_router');
        $this->assertEquals(
            '/news/test-news-article',
            $router->generate($metaLoader->load('article', ['contentPath' => '/swp/default/content/test-news-article']))
        );
    }
}
