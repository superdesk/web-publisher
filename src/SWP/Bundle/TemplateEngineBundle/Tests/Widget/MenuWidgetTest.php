<?php
/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplateEngineBundle\Tests\Widget;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Widget\MenuWidgetHandler;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;

class MenuWidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenusData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadMenuNodesData',
        ], null, 'doctrine_phpcr');
    }

    public function testMenuWidget()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test']);
        $widgetHandler = new MenuWidgetHandler($widgetModel);
        $widgetHandler->setContainer($this->getContainer());

        $content = $widgetHandler->render();
        $this->assertContains('<a href="http://example.com/home">Home</a>', $content);
        $this->assertContains('<a href="http://example.com/contact">Contact</a>', $content);
        $this->assertContains('<a href="http://example.com/contact/sub">Sub Contact</a>', $content);
    }
}
