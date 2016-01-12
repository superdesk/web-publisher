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
namespace SWP\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\FixturesBundle\AbstractFixture;

class LoadSitesData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $site = $manager->find('SWP\ContentBundle\Document\Site', '/swp/default');

        if (!$site) {
            throw new \Exception('Could not find /swp/default document!');
        }

        $page = $manager->find(null, '/swp/default/routes/homepage');

        $site->setHomepage($page);
        $manager->persist($page);

        $site = $manager->find('SWP\ContentBundle\Document\Site', '/swp/client1');

        if (!$site) {
            throw new \Exception('Could not find /swp/cllient document!');
        }

        $page2 = $manager->find(null, '/swp/client1/routes/homepage');

        $site->setHomepage($page2);
        $manager->persist($page2);
        $manager->flush();
    }
}
