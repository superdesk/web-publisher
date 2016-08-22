<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests\EventListener;


use Doctrine\ODM\PHPCR\Document\Generic;
use SWP\Bundle\ContentBundle\EventListener\ProcessArticleMediaListener;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Bridge\Model\Item;

class ProcessArticleMediaListenerTest extends WebTestCase
{
    /**
     * @var ProcessArticleMediaListener
     */
    protected $listener;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadRoutesData',
        ], null, 'doctrine_phpcr');

        $this->listener = new ProcessArticleMediaListener(
            $this->getContainer()->get('swp.object_manager.media'),
            $this->getContainer()->get('swp_multi_tenancy.path_builder'),
            $this->getContainer()->getParameter('swp_multi_tenancy.persistence.phpcr.media_basepath'),
            $this->getContainer()->get('swp_content_bundle.manager.media')
        );
    }

    /**
     * Test handling items under article
     */
    public function testOnArticleCreate()
    {
        $article = $observer = $this->getMockBuilder(ArticleInterface::class)->getMock();
        $article->expects($this->any())->method('getSlug')->willReturn('test-article');

        $this->assertInstanceOf(ArticleMediaInterface::class, $this->listener->handleMedia($article, new Generic(), 'test-key', new Item()));
    }
}
