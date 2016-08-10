<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Bundle\ContentBundle\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

class ArticleLoaderTest extends WebTestCase
{
    protected $articleLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
        ]);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $this->articleLoader = new ArticleLoader(
            $this->getContainer()->get('swp.publish_workflow.checker'),
            $this->getContainer()->get('doctrine_phpcr.odm.document_manager'),
            $this->getContainer()->getParameter('kernel.root_dir'),
            $this->getContainer()->get('doctrine_cache.providers.main_cache'),
            $this->getContainer()->get('swp_multi_tenancy.path_builder'),
            $this->getContainer()->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')
        );
    }

    public function testFindNewArticle()
    {
        $this->assertTrue($this->articleLoader->isSupported('article'));
        $this->assertTrue($this->articleLoader->isSupported('articles'));
        $this->assertFalse($this->articleLoader->isSupported('items'));

        $article = $this->articleLoader->load('article', ['contentPath' => '/swp/default/content/test-article']);
        $this->assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $article);

        $this->assertFalse($this->articleLoader->load('article', ['contentPath' => '/swp/default/content/test-articles']));
        $this->assertFalse($this->articleLoader->load('article', ['contentPath' => '/swp/default/content/test-article'], LoaderInterface::COLLECTION));

        $this->assertTrue(count($this->articleLoader->load('article', ['route' => '/news'], LoaderInterface::COLLECTION)) == 3);
        $this->assertFalse($this->articleLoader->load('article', ['route' => '/news1'], LoaderInterface::COLLECTION));

        $this->assertFalse($this->articleLoader->load('article', null, LoaderInterface::COLLECTION));
    }

    public function testLoadWithParameters()
    {
        $this->assertTrue(count($this->articleLoader->load('article', ['route' => '/news', 'limit' => 2], LoaderInterface::COLLECTION)) == 2);

        $articlesZero = $this->articleLoader->load('article', ['route' => '/news'], LoaderInterface::COLLECTION);
        $articlesOne = $this->articleLoader->load('article', ['route' => '/news', 'start' => 1], LoaderInterface::COLLECTION);

        $this->assertTrue($articlesZero[1]->title === $articlesOne[0]->title);

        $articlesAsc = $this->articleLoader->load('article', ['route' => '/news', 'order' => ['id', 'asc']], LoaderInterface::COLLECTION);
        $articlesDesc = $this->articleLoader->load('article', ['route' => '/news', 'order' => ['id', 'desc']], LoaderInterface::COLLECTION);

        $this->assertTrue(count($articlesAsc) == count($articlesDesc));

        $count = count($articlesAsc);
        $this->assertTrue($articlesAsc[0]->title === $articlesDesc[$count-1]->title);
        $this->assertTrue($articlesAsc[$count-1]->title === $articlesDesc[0]->title);
    }
}
