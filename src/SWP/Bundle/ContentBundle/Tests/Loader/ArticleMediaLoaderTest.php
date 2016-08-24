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
namespace SWP\Bundle\ContentBundle\Tests\Loader;

use SWP\Bundle\ContentBundle\Loader\ArticleMediaLoader;
use SWP\Bundle\FixturesBundle\WebTestCase;

class ArticleMediaLoaderTest extends WebTestCase
{
    /**
     * @var ArticleLoader
     */
    protected $articleLoader;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $this->articleLoader = new ArticleMediaLoader(
            $this->getContainer()->get('swp.publish_workflow.checker'),
            $this->getContainer()->get('doctrine_phpcr.odm.document_manager'),
            $this->getContainer()->getParameter('kernel.root_dir'),
            $this->getContainer()->get('doctrine_cache.providers.main_cache'),
            $this->getContainer()->get('swp_multi_tenancy.path_builder'),
            $this->getContainer()->get('swp_template_engine_context.factory.meta_factory'),
            $this->getContainer()->get('swp_template_engine_context')
        );
    }

    public function testIfIsSupported()
    {
        $this->assertTrue($this->articleLoader->isSupported('articleMedia'));
        $this->assertFalse($this->articleLoader->isSupported('articleMedias'));
    }
}
