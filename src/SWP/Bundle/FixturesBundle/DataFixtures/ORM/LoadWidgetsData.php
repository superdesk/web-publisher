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

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\CoreBundle\Model\WidgetModel;
use SWP\Component\MultiTenancy\Model\TenantInterface;

class LoadWidgetsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->container->get('swp.repository.tenant')->findOneByDomain(AbstractFixture::DEFAULT_TENANT_DOMAIN);

        $widget = new WidgetModel();
        $widget->setType(WidgetModel::TYPE_MENU);
        $widget->setName('NavigationMain');
        $widget->setParameters([
            'menu_name' => 'mainNavigation',
            'template_name' => 'menu1.html.twig',
        ]);
        $widget->setTenantCode($tenant->getCode());
        $manager->persist($widget);

        $this->addReference('menu_widget_main', $widget);

        $widget = new WidgetModel();
        $widget->setType(WidgetModel::TYPE_MENU);
        $widget->setName('NavigationFooterPrim');
        $widget->setParameters([
            'menu_name' => 'footerPrim',
            'template_name' => 'menu2.html.twig',
        ]);
        $widget->setTenantCode($tenant->getCode());
        $manager->persist($widget);

        $manager->flush();

        $this->addReference('menu_widget_footer', $widget);
    }

    public function getOrder(): int
    {
        return 2;
    }
}
