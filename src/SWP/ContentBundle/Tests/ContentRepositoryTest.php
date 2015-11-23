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
use SWP\ContentBundle\Document\Article;

class ContentRepositoryTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public function testFindNewArticle()
    {
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/PHPCR/Test/article.yml',
        ], true, null, 'doctrine_phpcr');

        $dm = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $articles = $dm->getRepository('SWP\ContentBundle\Document\Article')->findAll();
        $this->assertTrue(count($articles) === 2);

        $article = $dm->find('SWP\ContentBundle\Document\Article', '/swp/content/test-article');
        $this->assertInstanceOf('SWP\ContentBundle\Document\Article', $article);
    }
}
