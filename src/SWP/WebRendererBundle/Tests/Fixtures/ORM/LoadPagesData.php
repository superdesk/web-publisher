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

namespace SWP\WebRendererBundle\Tests\Fixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\WebRendererBundle\Entity\Page;

class LoadPagesData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $page = new Page();
        $page->setName('About us')
            ->setType(Page::PAGE_TYPE_CONTENT)
            ->setSlug('about-us')
            ->setTemplateName('static.html.twig')
            ->setContentPath('/content/about-us');

        $manager->persist($page);
        $manager->flush();
    }
}