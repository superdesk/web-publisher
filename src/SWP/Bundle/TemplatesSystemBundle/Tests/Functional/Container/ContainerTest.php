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

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Functional\Container;

use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

class ContainerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();
    }

    public function testContainer()
    {
        $container = $this->getContainer()->get('swp.factory.container')->create();
        self::assertInstanceOf($this->getContainer()->getParameter('swp.model.container.class'), $container);

        $container->setName('test_container');
        $this->getContainer()->get('swp.object_manager.container')->persist($container);
        $this->getContainer()->get('swp.object_manager.container')->flush();

        $repository = $this->getContainer()->get('swp.repository.container');
        $containerFromDatabase = $repository->getByName('test_container')->getQuery()->getSingleResult();

        self::assertSame('test_container', $containerFromDatabase->getName());
    }

    public function testContainerData()
    {
        $containerDataFactory = $this->getContainer()->get('swp.factory.container_data');
        $objectManager = $this->getContainer()->get('swp.object_manager.container');
        $container = $this->getContainer()->get('swp.factory.container')->create();
        $container->setName('test_container');

        $sampleData = $containerDataFactory->create('test_key', 'test_value');
        $objectManager->persist($sampleData);
        $container->addData($sampleData);
        $objectManager->persist($container);
        $objectManager->flush();

        $repository = $this->getContainer()->get('swp.repository.container');
        /** @var ContainerInterface $containerFromDatabase */
        $containerFromDatabase = $repository->getByName('test_container')->getQuery()->getSingleResult();

        self::assertCount(1, $containerFromDatabase->getData());
    }
}
