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
        $defaultTenantPrefix = $this->getTenantPrefix();
        $firstTenantPrefix = $this->getTenantPrefix('client1');

        $site = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site', $defaultTenantPrefix);

        if (!$site) {
            throw new \Exception(sprintf('Could not find %s document!', $defaultTenantPrefix));
        }

        $page = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', $defaultTenantPrefix.'/routes/homepage');
        $site->setHomepage($page);

        $site = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site', $firstTenantPrefix);

        if (!$site) {
            throw new \Exception(sprintf('Could not find %s document!', $firstTenantPrefix));
        }

        $page2 = $manager->find('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route', $firstTenantPrefix.'/routes/homepage');
        $site->setHomepage($page2);
        $manager->flush();
    }
}
