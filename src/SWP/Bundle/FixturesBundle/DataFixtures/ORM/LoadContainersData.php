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

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\Container;

class LoadContainersData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $containerEntity = new Container();
        $containerEntity->setName('container_name');
        $containerEntity->setStyles('border: solid 1px red');
        $containerEntity->setCssClass('col-md-12');
        $manager->persist($containerEntity);
        $manager->flush();

        $this->addReference('container_name', $containerEntity);
    }

    public function getOrder()
    {
        return 1;
    }
}
