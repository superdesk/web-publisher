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

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\FixturesBundle\WebTestCase;

class ArticleAuthorLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var WebTestCase
     */
    private $client;

    /**
     * SetUp test.
     */
    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant', 'article']);

        $this->twig = $this->getContainer()->get('twig');
        $this->client = static::createClient();
    }

    public function testRenderingArticleAuthors()
    {
        $template = '{% gimmelist author from authors %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom  Test Person  John Doe  John Doe  Test Person ', $result);
    }

    public function testRenderingArticleAuthorsWith()
    {
        $template = '{% gimmelist author from authors with {jobtitle: {name: "quality check"}} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);

        $template = '{% gimmelist author from authors with {role: ["Writer"]} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom  Test Person  John Doe  John Doe  Test Person ', $result);
    }

    public function testRenderingArticleAuthorsWithout()
    {
        $template = '{% gimmelist author from authors without {role: ["Writer"]} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);
    }

    public function testRenderingArticleAuthorsWithoutAndWith()
    {
        $template = '{% gimmelist author from authors with {role: ["Writer"]} without {role: ["Writer"]} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);
    }

    public function testLoadAuthorById()
    {
        $template = '{% gimme author with {id: 1} %} {{ author.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom ', $result);
    }

    public function testLoadAuthorByName()
    {
        $template = '{% gimme author with {name: "Tom"} %} {{ author.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
