<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\CoreBundle\Model\WidgetModel;
use SWP\Bundle\CoreBundle\Widget\ContentListWidget;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;

final class ContentListWidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant', 'container']);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
        ], true, null, 'doctrine', 0);

        $this->getContainer()->get('swp_multi_tenancy.tenant_context')
            ->setTenant($this->getContainer()->get('swp.repository.tenant')->findOneByCode('123abc'));
        $this->getContainer()->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        $this->getContainer()->get('swp_revision.context.revision')
            ->setCurrentRevision($this->getContainer()->get('swp.repository.revision')->getPublishedRevision()->getQuery()->getOneOrNullResult());
    }

    public function testContentListWidgetRendering()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setType(WidgetModel::TYPE_LIST);
        $widgetModel->setId(1);
        $widgetModel->setParameters(['list_name' => 'List1', 'template_name' => 'list.html.twig']);
        $widgetHandler = new ContentListWidget($widgetModel, $this->getContainer());
        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="testContainerId">', $widgetHandler->renderWidgetOpenTag('testContainerId'));
        self::assertEquals('1 List1', trim($widgetHandler->render()));
    }

    public function testContainerRendererRenderingWithContentList()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setType(WidgetModel::TYPE_LIST);
        $widgetModel->setId(1);
        $widgetModel->setParameters(['list_name' => 'List1', 'template_name' => 'list.html.twig']);
        $widgetHandler = new ContentListWidget($widgetModel, $this->getContainer());

        $containerRenderer = $this->getContainer()
            ->get('swp_template_engine.container.renderer')
            ->getContainerRenderer('Simple Container 1');
        $containerRenderer->setWidgets([$widgetHandler]);

        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="5tfdv6resqg">1 List1</div>', $containerRenderer->renderWidgets());
    }
}
