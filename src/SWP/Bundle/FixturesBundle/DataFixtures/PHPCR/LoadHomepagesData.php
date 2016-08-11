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
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use SWP\Bundle\CoreBundle\Doctrine\ODM\PHPCR\Tenant;

class LoadHomepagesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $tenants = [
            $manager->find(Tenant::class, '/swp/123456/123abc'),
            $manager->find(Tenant::class, '/swp/654321/456def'),
        ];

        $this->createHomepages($manager, $tenants);

        foreach ($tenants as $site) {
            $page = $manager->find(
                Route::class,
                $site->getId().'/routes/homepage'
            );

            $site->setHomepage($page);
        }

        $manager->flush();
    }

    private function createHomepages(ObjectManager $manager, array $tenants)
    {
        foreach ($tenants as $site) {
            $route = new Route();
            $route->setParentDocument($manager->find(null, $site->getId().'/routes'));
            $route->setName('homepage');
            $route->setType(RouteInterface::TYPE_CONTENT);

            $manager->persist($route);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 6;
    }
}
