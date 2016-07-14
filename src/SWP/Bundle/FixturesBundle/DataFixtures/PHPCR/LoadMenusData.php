<?php
/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

class LoadMenusData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();
        $menus = [
            'dev' => [

            ],
            'test' => [
                [
                    'name' => 'test',
                    'label' => 'Test',
                ],
            ],
        ];

        if (isset($menus[$env])) {
            $parent = $manager->find(null, 'swp/default/menu');
            foreach ($menus[$env] as $menuData) {
                $menu = new Menu();
                $menu->setParentDocument($parent);
                $menu->setName($menuData['name']);
                $menu->setLabel($menuData['label']);
                $manager->persist($menu);
            }
        }

        $manager->flush();
    }
}
