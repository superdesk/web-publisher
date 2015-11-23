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
namespace SWP\ContentBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\ContentBundle\Loader\ArticleLoader;
use SWP\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\ContentBundle\Document\Article;

class ArticleLoaderTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
            '@SWPFixturesBundle/DataFixtures/ORM/Test/pagecontent.yml',
        ]);

        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public function testFindNewArticle()
    {
        $manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Test Article lipsum');
        $manager->persist($article);
        $manager->flush();

        $articleLoader = new ArticleLoader(
            $this->getContainer()->getParameter('kernel.root_dir'),
            $this->getContainer()->get('doctrine_phpcr.odm.document_manager'),
            $this->getContainer()->get('doctrine.orm.entity_manager')
        );

        $this->assertTrue($articleLoader->isSupported('article'));
        $this->assertTrue($articleLoader->isSupported('articles'));
        $this->assertFalse($articleLoader->isSupported('items'));

        $article = $articleLoader->load('article', ['contentPath' => '/swp/content/test-article']);
        $this->assertInstanceOf('SWP\TemplatesSystem\Gimme\Meta\Meta', $article);

        $this->assertFalse($articleLoader->load('article', ['contentPath' => '/swp/content/test-articles']));
        $this->assertFalse($articleLoader->load('article', ['contentPath' => '/swp/content/test-article'], LoaderInterface::COLLECTION));

        $article = new Article();
        $article->setTitle('Features');
        $article->setContent('Features ipsum');
        $manager->persist($article);
        $manager->flush();

        $this->assertTrue(count($articleLoader->load('article', ['pageName' => 'News'], LoaderInterface::COLLECTION)) == 1);
        $this->assertFalse($articleLoader->load('article', ['pageName' => 'Features'], LoaderInterface::COLLECTION));

        $this->assertFalse($articleLoader->load('article', null, LoaderInterface::COLLECTION));
    }
}
