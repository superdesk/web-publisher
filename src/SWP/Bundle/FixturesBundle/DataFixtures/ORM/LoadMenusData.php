<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadMenusData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();
        $menus = [
            'dev' => [
                [
                    'name' => 'mainNavigation',
                    'label' => 'Main Navigation',
                ],
                [
                    'name' => 'footerPrim',
                    'label' => 'Primary Footer Navigation',
                ],
            ],
            'test' => [
                [
                    'name' => 'test',
                    'label' => 'Test',
                ],
            ],
        ];

        if (isset($menus[$env])) {
            $factory = $this->container->get('swp.factory.menu');
            foreach ($menus[$env] as $menuData) {
                $menu = $factory->createItem($menuData['name']);
                $menu->setLabel($menuData['label']);
                $manager->persist($menu);
            }
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 4;
    }
}
