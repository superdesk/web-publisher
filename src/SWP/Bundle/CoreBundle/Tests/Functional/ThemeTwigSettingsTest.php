<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Loader;

use SWP\Bundle\FixturesBundle\WebTestCase;

class ThemeTwigSettingsTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testRenderingThemeSettings()
    {
        $template = '{{ themeSetting(\'primary_font_family\') }}';
        $result = $this->getRendered($template);

        self::assertContains('Roboto', $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testRenderingNotExistingThemeSettings()
    {
        $template = '{{ themeSetting(\'fake\') }}';
        $this->getRendered($template);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
