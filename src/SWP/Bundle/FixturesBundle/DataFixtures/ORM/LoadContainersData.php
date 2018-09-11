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
use SWP\Bundle\CoreBundle\Model\Container;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContainersData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $env = $this->getEnvironment();
        if ('dev' === $env) {
            $revision = $manager->merge($this->getReference('default_tenant_revision'));

            $container1 = $this->container->get('swp.factory.container')->create();
            $container1->setName('mainNav');
            $container1->setType(1);
            $container1->setVisible(true);
            $container1->setTenantCode('123abc');
            $container1->setRevision($revision);

            $containerWidget1 = $this->container->get('swp.factory.container_widget')
                ->create($container1, $this->getReference('menu_widget_main'));
            $manager->persist($containerWidget1);
            $container1->addWidget($containerWidget1);
            $manager->persist($container1);

            $container2 = $this->container->get('swp.factory.container')->create();
            $container2->setName('footerNav');
            $container2->setType(1);
            $container2->setVisible(true);
            $container2->setTenantCode('123abc');
            $container2->setRevision($revision);

            $containerWidget2 = $this->container->get('swp.factory.container_widget')
                ->create($container2, $this->getReference('menu_widget_footer'));
            $manager->persist($containerWidget2);
            $container2->addWidget($containerWidget2);
            $manager->persist($container2);

            $manager->flush();
        } else {
            $revision = $manager->merge($this->getReference('default_tenant_revision'));

            /** @var Container $container1 */
            $container1 = $this->container->get('swp.factory.container')->create();
            $container1->setName('Simple Container 1');
            $container1->setType(1);
            $container1->setStyles('color: #00000');
            $container1->setCssClass('col-md-12');
            $container1->setTenantCode('123abc');
            $container1->setVisible(true);
            $container1->setRevision($revision);

            /** @var Container $container2 */
            $container2 = $this->container->get('swp.factory.container')->create();
            $container2->setName('Simple Container 2');
            $container2->setType(1);
            $container2->setStyles('border: 1px solid red;');
            $container2->setCssClass('col-md-6');
            $container2->setTenantCode('123abc');
            $container1->setVisible(true);
            $container2->setRevision($revision);
        }
    }

    public function getOrder(): int
    {
        return 2;
    }
}
