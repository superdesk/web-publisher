<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests\Service;

use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\FixturesBundle\WebTestCase;

class RouteToArticleMapperTest extends WebTestCase
{
    protected $defaultData;

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
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadRouteToArticlesData',
        ]);
    }

    public function testAssignRoutetoArticle()
    {
        $routeToArticleMapper = $this->getContainer()->get('swp_content_bundle.routetoarticle.mapper');

        $article = new Article();
        $article->setLocale('en');

        $routeToArticleMapper->assignRouteToArticle($article);
    }
}
