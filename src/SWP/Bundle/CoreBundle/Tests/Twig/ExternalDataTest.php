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

class ExternalDataTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'metadata_articles']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testRenderingExtraFields()
    {
        $template = '{% gimmelist article from articles %} {{ article.packageExternalData[\'some test data\'] }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertStringContainsString('SOME TEST VALUE', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
