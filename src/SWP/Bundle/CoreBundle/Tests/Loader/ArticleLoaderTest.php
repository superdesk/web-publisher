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

class ArticleLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant', 'article']);

        $this->twig = $this->getContainer()->get('twig');
    }

    public function testFilteringByKeyword()
    {
        $template = '{% gimmelist article from articles with {keywords: ["car"]} %} {% for keyword in article.keywords %} {{ keyword }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('car', $result);
        self::assertNotContains('mazda', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
