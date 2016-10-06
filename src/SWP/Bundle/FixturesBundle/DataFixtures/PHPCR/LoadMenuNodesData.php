<?php

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

namespace SWP\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class LoadMenuNodesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $defaultTenantPrefix = $this->getTenantPrefix();

        $env = $this->getEnvironment();
        $menuNodes = [
            'dev' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'route' => 'homepage',
                    'parent' => $defaultTenantPrefix.'/menu/default',
                ],
            ],
            'test' => [
                [
                    'name' => 'home',
                    'label' => 'Home',
                    'locale' => 'en',
                    'uri' => 'http://example.com/home',
                ],
                [
                    'name' => 'contact',
                    'label' => 'Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact',
                ],
                [
                    'name' => 'sub',
                    'label' => 'Sub Contact',
                    'locale' => 'en',
                    'uri' => 'http://example.com/contact/sub',
                    'parent' => $defaultTenantPrefix.'/menu/test/contact',
                ],
            ],
        ];

        if (isset($menuNodes[$env])) {
            $defaultParent = $env === 'test' ? $manager->find(null, $defaultTenantPrefix.'/menu/test') : $manager->find(null, $defaultTenantPrefix.'/menu/default');
            foreach ($menuNodes[$env] as $menuNodeData) {
                $menuNode = new MenuNode();
                $menuNode->setName($menuNodeData['name']);
                $menuNode->setLabel($menuNodeData['label']);
                $menuNode->setLocale($menuNodeData['locale']);
                if (isset($menuNodeData['uri'])) {
                    $menuNode->setUri($menuNodeData['uri']);
                }
                if (isset($menuNodeData['route'])) {
                    $menuNode->setRoute($menuNodeData['route']);
                }
                $parent = isset($menuNodeData['parent']) ? $manager->find(null, $menuNodeData['parent']) : $defaultParent;
                $menuNode->setParentDocument($parent);
                $manager->persist($menuNode);
                $manager->flush();
            }
        }
    }

    public function getOrder()
    {
        return 5;
    }
}
