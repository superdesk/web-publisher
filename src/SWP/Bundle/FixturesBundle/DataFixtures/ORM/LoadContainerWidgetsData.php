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
use SWP\Bundle\TemplateEngineBundle\Model\ContainerWidget;

class LoadContainerWidgetsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if ('test' !== $this->getEnvironment()) {
            $containerEntity = $this->getReference(LoadContainersData::MAIN_CONTAINER_NAME);
            $containerMenuWidget = $this->getReference(LoadWidgetsData::MAIN_NAV_MENU_NAME);
            $containerWidget = new ContainerWidget($containerEntity, $containerMenuWidget);
            $containerEntity->addWidget($containerWidget);

            $manager->persist($containerEntity);
            $manager->persist($containerWidget);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 3;
    }
}
