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

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\FixturesBundle\WebTestCase;

class StringyExtensionTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadCustomFixtures(['tenant']);

        $this->twig = $this->getContainer()->get('twig');
    }

    public function testCamelize()
    {
        $this->assertEquals($this->getRendered('{{ value|camelize }}', ['value' => 'AROOGA']), 'aROOGA');
    }

    public function testBetween()
    {
        $this->assertEquals($this->getRendered("{{ value|between('AR', 'GA') }}", ['value' => 'AROOGA']), 'OO');
    }

    public function testCollapseWhiteSpace()
    {
        $this->assertEquals($this->getRendered('{{ value|collapseWhitespace }}', ['value' => 'AR      OO     GA']), 'AR OO GA');
    }

    public function testUpperCase()
    {
        $this->assertEquals($this->getRendered('{% if hasUpperCase(value) %}AROOGA{% endif %}', ['value' => 'AROOGA']), 'AROOGA');
    }

    public function testContains()
    {
        $this->assertEquals($this->getRendered("{% if contains(value, 'OOG') %}AROOGA{% endif %}", ['value' => 'AROOGA']), 'AROOGA');
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
