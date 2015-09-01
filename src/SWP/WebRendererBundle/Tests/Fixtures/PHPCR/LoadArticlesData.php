<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\WebRendererBundle\Tests\Fixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\ContentBundle\Document\Article;


class LoadArticlesData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $article = new Article();
        $article->setTitle("About us");
        $manager->persist($article);
        $manager->flush();
    }
}
