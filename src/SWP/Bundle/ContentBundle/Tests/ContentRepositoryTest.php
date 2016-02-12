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

class ContentRepositoryTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public function testFindNewArticle()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $dm = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $articles = $dm->getRepository('SWP\Bundle\ContentBundle\Document\Article')->findAll();
        $this->assertTrue(count($articles) === 4);
        $article = $dm->find('SWP\Bundle\ContentBundle\Document\Article', '/swp/default/content/test-article');
        $this->assertInstanceOf('SWP\Bundle\ContentBundle\Document\Article', $article);

        $article = $dm->find('SWP\Bundle\ContentBundle\Document\Article', '/swp/client1/content/features-client1');
        $this->assertInstanceOf('SWP\Bundle\ContentBundle\Document\Article', $article);
    }
}
