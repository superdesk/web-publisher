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

use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Component\TemplatesSystem\Gimme\Widget\GoogleAdSenseWidgetHandler;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdSenseWidgetTest extends WebTestCase
{
    private $widget;

    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

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
