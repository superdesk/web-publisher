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
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true, null, 'doctrine', 0);

        $this->getContainer()->get('swp_multi_tenancy.tenant_context')
            ->setTenant($this->getContainer()->get('swp.repository.tenant')->findOneByCode('123abc'));
        $this->getContainer()->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        $this->getContainer()->get('swp_revision.context.revision')
            ->setCurrentRevision($this->getContainer()->get('swp.repository.revision')->getPublishedRevision()->getQuery()->getOneOrNullResult());
    }

    protected function getContentListWidget($templateName = 'list.html.twig')
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setType(WidgetModel::TYPE_LIST);
        $widgetModel->setId(1);
        $widgetModel->setParameters(['list_name' => 'List1', 'template_name' => $templateName]);

        return new ContentListWidget($widgetModel, $this->getContainer());
    }

    public function testContentListWidgetRendering()
    {
        $widgetHandler = $this->getContentListWidget();

        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="testContainerId">', $widgetHandler->renderWidgetOpenTag('testContainerId'));
        self::assertEquals('1 List1', trim($widgetHandler->render()));
    }

    public function testContainerRendererRenderingWithContentList()
    {
        $widgetHandler = $widgetHandler = $this->getContentListWidget();

        $containerRenderer = $this->getContainer()
            ->get('swp_template_engine.container.renderer')
            ->getContainerRenderer('Simple Container 1');
        $containerRenderer->setWidgets([$widgetHandler]);

        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="5tfdv6resqg">1 List1</div>', $containerRenderer->renderWidgets());
    }

    public function testContentListCacheAfterListUpdate()
    {
        $widgetHandler = $this->getContentListWidget('list_with_items.html.twig');

        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="testContainerId">', $widgetHandler->renderWidgetOpenTag('testContainerId'));
        self::assertEquals(<<<'EOT'
<ul>
    <li>article1-0-true</li>
    <li>article3-2-true</li>
    <li>article2-1-false</li>
    <li>article4-3-false</li>
</ul>
EOT
, trim($widgetHandler->render()));
        sleep(2);
        static::createClient()->request('PATCH', $this->getContainer()->get('router')->generate('swp_api_core_update_lists_item', [
            'id' => 3,
            'listId' => 1,
        ]), [
            'content_list_item' => [
                'sticky' => false,
            ],
        ]);

        $this->getContainer()->get('doctrine.orm.entity_manager')->clear();
        $widgetHandler = $this->getContentListWidget('list_with_items.html.twig');
        self::assertEquals(<<<'EOT'
<ul>
    <li>article1-0-true</li>
    <li>article2-1-false</li>
    <li>article3-2-false</li>
    <li>article4-3-false</li>
</ul>
EOT
            , trim($widgetHandler->render()));
    }

    public function testContentListCacheAfterItemContentUpdate()
    {
        $widgetHandler = $this->getContentListWidget('list_with_items.html.twig');

        self::assertEquals('<div id="swp_widget_1" class="swp_widget" data-widget-type="contentlist" data-list-type="automatic" data-list-id="1" data-container="testContainerId">', $widgetHandler->renderWidgetOpenTag('testContainerId'));
        self::assertEquals(<<<'EOT'
<ul>
    <li>article1-0-true</li>
    <li>article3-2-true</li>
    <li>article2-1-false</li>
    <li>article4-3-false</li>
</ul>
EOT
            , trim($widgetHandler->render()));

        $client = static::createClient();
        $router = $this->getContainer()->get('router');
        $client->request('PATCH', $router->generate('swp_api_content_update_articles', ['id' => 'article-1']), [
            'article' => [
                'status' => 'unpublished',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $this->getContainer()->get('doctrine.orm.entity_manager')->clear();
        $widgetHandler = $this->getContentListWidget('list_with_items.html.twig');
        self::assertEquals(<<<'EOT'
<ul>
    <li>article3-1-true</li>
    <li>article2-0-false</li>
    <li>article4-2-false</li>
</ul>
EOT
            , trim($widgetHandler->render()));
    }
}
