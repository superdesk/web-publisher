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
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Component\MultiTenancy\Model\TenantInterface;

class LoadWidgetsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->container->get('swp.repository.tenant')->findOneBySubdomain('default');

        $widget = new WidgetModel();
        $widget->setType(WidgetModel::TYPE_MENU);
        $widget->setName('Default Menu');
        $widget->setParameters(['menu_name' => 'default']);
        $widget->setTenantCode($tenant->getCode());
        $manager->persist($widget);
        $manager->flush();

        $this->addReference('container_name_menu_widget', $widget);
    }

    public function getOrder()
    {
        return 2;
    }
}
