<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;

class ContentRepositoryTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');
    }

    public function testFindNewArticle()
    {
        $dm = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $articles = $dm->getRepository('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article')->findAll();
        $this->assertTrue(count($articles) === 4);
        $article = $dm->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article', '/swp/123456/123abc/content/test-article');
        $this->assertInstanceOf('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article', $article);

        $article = $dm->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article', '/swp/654321/456def/content/features-client1');
        $this->assertInstanceOf('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article', $article);
    }
}
