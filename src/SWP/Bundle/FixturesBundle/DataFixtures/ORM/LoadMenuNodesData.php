<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\CoreBundle\Model\MenuItem;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class LoadMenuNodesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();
        $menuNodes = [
            'dev' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'uri' => '/',
                ],
                [
                    'name' => 'politics',
                    'label' => 'Politics',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'politics',
                ],
                [
                    'name' => 'business',
                    'label' => 'Business',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'business',
                ],
                [
                    'name' => 'scitech',
                    'label' => 'Sci/Tech',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'scitech',
                ],
                [
                    'name' => 'health',
                    'label' => 'Health',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'health',
                ],
                [
                    'name' => 'entertainment',
                    'label' => 'Entertainment',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'entertainment',
                ],
                [
                    'name' => 'sports',
                    'label' => 'Sports',
                    'locale' => 'en',
                    'parent' => 'mainNavigation',
                    'route' => 'sports',
                ],
                [
                    'name' => 'politics',
                    'label' => 'Politics',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'politics',
                ],
                [
                    'name' => 'business',
                    'label' => 'Business',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'business',
                ],
                [
                    'name' => 'scitech',
                    'label' => 'Sci/Tech',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'scitech',
                ],
                [
                    'name' => 'health',
                    'label' => 'Health',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'health',
                ],
                [
                    'name' => 'entertainment',
                    'label' => 'Entertainment',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'entertainment',
                ],
                [
                    'name' => 'sports',
                    'label' => 'Sports',
                    'locale' => 'en',
                    'parent' => 'footerPrim',
                    'route' => 'sports',
                ],
            ],
            'test' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'uri' => 'http://example.com/home',
                    'parent' => 'test',
                ],
                [
                    'name' => 'contact',
                    'label' => 'Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact',
                    'parent' => 'test',
                ],
                [
                    'name' => 'sub',
                    'label' => 'Sub Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact/sub',
                    'parent' => 'contact',
                ],
            ],
        ];

        if (isset($menuNodes[$env])) {
            $factory = $this->container->get('swp.factory.menu');
            $repository = $this->container->get('swp.repository.menu');
            $routeRepository = $this->container->get('swp.repository.route');
            foreach ($menuNodes[$env] as $menuNodeData) {
                /** @var MenuItem $menuNode */
                $menuNode = $factory->createItem($menuNodeData['name']);
                if (isset($menuNodeData['route'])) {
                    $route = $routeRepository->findOneBy(['name' => $menuNodeData['route']]);
                    /** @var MenuItem $menuNode */
                    $menuNode = $factory->createItem($menuNodeData['name'], ['route' => $route ? $route->getName() : null]);
                    $menuNode->setRoute($route);
                }

                $menuNode->setLabel($menuNodeData['label']);
                if (isset($menuNodeData['uri'])) {
                    $menuNode->setUri($menuNodeData['uri']);
                }

                if (isset($menuNodeData['parent'])) {
                    $menuNode->setParent($repository->getOneMenuItemByName($menuNodeData['parent']));
                }

                $manager->persist($menuNode);
                $manager->flush();
            }
        }
    }

    public function getOrder(): int
    {
        return 5;
    }
}
