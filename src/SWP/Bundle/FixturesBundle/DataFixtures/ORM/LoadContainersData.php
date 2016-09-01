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
namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\TemplateEngineBundle\Model\Container;

class LoadContainersData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const MAIN_CONTAINER_NAME = 'mainNavigation';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if ('test' !== $this->getEnvironment()) {
            $containerEntity = new Container();
            $containerEntity->setName(self::MAIN_CONTAINER_NAME);
            $containerEntity->setStyles('border: dotted 1px #ccc');
            $containerEntity->setCssClass('swp_container');
            $manager->persist($containerEntity);
            $manager->flush();

            $this->addReference(self::MAIN_CONTAINER_NAME, $containerEntity);
        }
    }

    public function getOrder()
    {
        return 1;
    }
}
