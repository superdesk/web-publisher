<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadSitesData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $site = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site', '/swp/default');

        if (!$site) {
            throw new \Exception('Could not find /swp/default document!');
        }

        $page = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', '/swp/default/routes/homepage');
        $site->setHomepage($page);

        $site = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site', '/swp/client1');

        if (!$site) {
            throw new \Exception('Could not find /swp/client1 document!');
        }

        $page2 = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', '/swp/client1/routes/homepage');
        $site->setHomepage($page2);
        $manager->flush();
    }
}
