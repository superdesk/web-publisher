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
namespace SWP\ContentBundle\Tests\Twig\Extension;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\ContentBundle\Twig\Extension\ContentExtension;
use SWP\ContentBundle\Document\Article;
use SWP\TemplatesSystem\Gimme\Meta\Meta;

class ContentExtensionTest extends WebTestCase
{
    public function testGetFunctions()
    {
        $contentExtension = new ContentExtension(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->get('router')
        );
        $functions = $contentExtension->getFunctions();

        $generateUrlFor = false;
        foreach ($functions as $name => $function) {
            if ($function instanceof \Twig_SimpleFunction) {
                if ($function->getName() == 'gimmeUrl') {
                    $generateUrlFor = true;
                }
            }
        }

        $this->assertTrue($generateUrlFor, 'Should find "gimmeUrl" to be a valid function');
    }

    public function testGetName()
    {
        $contentExtension = new ContentExtension(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->get('router')
        );
        $this->assertEquals('swp_content', $contentExtension->getName(), 'Should have name "swp_content"');
    }

    public function testGenerateUrlFor()
    {
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
            '@SWPFixturesBundle/DataFixtures/ORM/Test/pagecontent.yml',
        ]);

        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $article = new Article();
        $article->setTitle('Features');
        $article->setContent('Features ipsum');
        $manager->persist($article);
        $manager->flush();

        $this->assertTrue($article->getTitle() === 'Features');

        $contentExtension = new ContentExtension(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->get('router')
        );

        $articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $featuresUrl = $contentExtension->gimmeUrl($articleLoader->load('article', ['contentPath' => '/swp/content/features']));
        $this->assertEquals('/news/features', $featuresUrl, 'Should generate url for Features article under News page: /news/features');

        $wrongObject = $contentExtension->gimmeUrl([]);
        $this->assertEquals(false, $wrongObject, 'Should return false when wrong object is passed');

        $wrongObject = $contentExtension->gimmeUrl(new Meta(
            $this->getContainer()->getParameter('kernel.root_dir').'/Resources/meta/article.yml',
            []
        ));
        $this->assertEquals(false, $wrongObject, 'Should return false when meta with vrong values object is passed');

        $article = new Article();
        $article->setTitle('Contact');
        $article->setContent('Contact lipsum ');
        $manager->persist($article);
        $manager->flush();

        $wrongUrlType = $contentExtension->gimmeUrl($articleLoader->load('article', ['contentPath' => '/swp/content/contact']));
        $this->assertFalse($wrongUrlType, 'Should return false for not existing pageArticle');

        $article = new Article();
        $article->setTitle('Test Article');
        $article->setContent('Test Article lipsum ');
        $manager->persist($article);
        $manager->flush();
        $childPageUrl = $contentExtension->gimmeUrl($articleLoader->load('article', ['contentPath' => '/swp/content/test-article']));
        $this->assertEquals('/news/sport/test-article', $childPageUrl, 'Should generate url for Test Article article under Sport page with News as a parent page: /news/sport/test-article');
    }
}
