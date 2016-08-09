<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Tests\Gimme\Widget;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Component\TemplatesSystem\Gimme\Widget\GoogleAdSenseWidgetHandler;

class AdSenseWidgetTest extends WebTestCase
{
    private $widget;

    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $this->runCommand('theme:setup', ['--env' => 'test'], true);

        $widgetEntity = new WidgetModel();
        $widgetEntity->setId(1);
        $widgetEntity->setParameters(['ad_client' => '1', 'ad_slot' => '1']);

        $this->widget = new GoogleAdSenseWidgetHandler($widgetEntity);
        $this->widget->setContainer($this->getContainer());
    }

    public function testCheckingVisibility()
    {
        $this->assertTrue($this->widget->isVisible());
    }

    public function testRendering()
    {
        $rendered = $this->widget->render();
        $this->assertContains('data-ad-client="1"', $rendered);
        $this->assertContains('data-ad-slot="1"', $rendered);
    }
}
