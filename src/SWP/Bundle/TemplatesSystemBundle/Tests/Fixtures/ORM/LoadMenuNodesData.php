<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Fixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMenuNodesData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $menuNodes = [
            [
                'name' => 'home',
                'label' => 'Home',
                'locale' => 'en',
                'uri' => 'http://example.com/home',
                'parent' => 1,
            ],
            [
                'name' => 'contact',
                'label' => 'Contact',
                'locale' => 'en',
                'uri' => 'http://example.com/contact',
                'parent' => 1,
            ],
            [
                'name' => 'sub',
                'label' => 'Sub Contact',
                'locale' => 'en',
                'uri' => 'http://example.com/contact/sub',
                'parent' => 3,
            ],
        ];

        $factory = $this->container->get('swp.factory.menu');
        $repository = $this->container->get('swp.repository.menu');
        foreach ($menuNodes as $menuNodeData) {
            $menuNode = $factory->createItem($menuNodeData['name']);
            $menuNode->setLabel($menuNodeData['label']);
            if (isset($menuNodeData['uri'])) {
                $menuNode->setUri($menuNodeData['uri']);
            }
            if (isset($menuNodeData['route'])) {
                $menuNode->setRoute($menuNodeData['route']);
            }
            if (isset($menuNodeData['parent'])) {
                $menuNode->setParent($repository->getOneMenuItemById($menuNodeData['parent']));
            }

            $manager->persist($menuNode);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 5;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
