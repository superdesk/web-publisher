<?php

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\AbstractFixture;

class LoadMenusData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
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

    public function getOrder()
    {
        return 4;
    }
}
