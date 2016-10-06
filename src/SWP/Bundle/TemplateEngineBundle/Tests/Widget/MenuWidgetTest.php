<?php
/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplateEngineBundle\Tests\Widget;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\TemplateEngineBundle\Widget\MenuWidgetHandler;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;

class MenuWidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenusData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenuNodesData',
        ], null, 'doctrine_phpcr');
    }

    public function testMenuWidget()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test']);
        $widgetHandler = new MenuWidgetHandler($widgetModel, $this->getContainer()->get('templating'));

        $content = $widgetHandler->render();
        $this->assertContains('<a href="http://example.com/home">Home</a>', $content);
        $this->assertContains('<a href="http://example.com/contact">Contact</a>', $content);
        $this->assertContains('<a href="http://example.com/contact/sub">Sub Contact</a>', $content);
    }
}
