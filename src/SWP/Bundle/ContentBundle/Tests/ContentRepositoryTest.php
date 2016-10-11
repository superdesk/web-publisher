<?php

/*
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

use Doctrine\Common\DataFixtures\ReferenceRepository;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;

class ContentRepositoryTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        $this->fixtures = $this->loadCustomFixtures(['tenant', 'article']);
    }

    public function testFindNewArticle()
    {
        self::assertCount(4, $this->fixtures->getReferences());
        self::assertInstanceOf(ArticleInterface::class, $this->fixtures->getReference('test-news-article'));
        self::assertInstanceOf(ArticleInterface::class, $this->fixtures->getReference('features-client1'));
    }
}
