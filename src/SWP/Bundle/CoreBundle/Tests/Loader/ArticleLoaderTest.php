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

class ArticleLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testRenderingExtraFields()
    {
        $template = '{% gimmelist article from articles %} {{ article.extra[\'custom-field\'] }}  {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('my custom field', $result);
    }

    public function testFilteringByKeyword()
    {
        $template = '{% gimmelist article from articles with {keywords: ["car"]} %} {% for keyword in article.keywords %} {{ keyword }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('car', $result);
        self::assertNotContains('mazda', $result);
    }

    public function testFilteringByMultipleRoutes()
    {
        $template = '{% gimmelist article from articles with {"route": [3, 5]} %} {{ article.route.id }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('3', $result);
        self::assertContains('5', $result);

        $template = '{% gimmelist article from articles with {"route": [1, 2]} %} {{ article.route.id }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals('', $result);
    }

    public function testFilteringByMultipleRoutesNames()
    {
        $context = $this->getContainer()->get('context');
        $template = '{% gimmelist article from articles with {"route": ["/news", "/articles"]} %} {{ article.route.staticPrefix }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertContains('/news', $result);

        $context->reset();
        $template = '{% gimmelist article from articles with {"route": ["/articles"]} %} {{ article.route.staticPrefix }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);

        $context->reset();
        $template = '{% gimmelist article from articles with {"route": ["/news", "/news/sports"]} %} {{ article.route.staticPrefix }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertContains('/news', $result);
        self::assertContains('/news/sports', $result);

        $context->reset();
        $template = '{% gimmelist article from articles with {"route": ["/news/*"]} %} {{ article.route.staticPrefix }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertContains('/news', $result);
        self::assertContains('/news/sports', $result);
    }

    public function testFilteringBySources()
    {
        $template = '{% gimmelist article from articles with {source: ["Forbes"]} ignoreContext ["route"] %} {% for source in article.sources %} {{ source.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertContains('Forbes', $result);
        self::assertNotContains('Reuters', $result);

        $template = '{% gimmelist article from articles ignoreContext ["route"] if article.sources is empty %} {% for source in article.sources %} {{ source.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEmpty($result);
    }

    public function testFilteringByExcludedSources()
    {
        $template = '{% gimmelist article from articles with {source: ["Forbes"]} without {source: ["AAP"]} %} {% for source in article.sources %} {{ source.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('Forbes', $result);
        self::assertNotContains('AAP', $result);
    }

    public function testFilteringByAuthors()
    {
        $template = '{% gimmelist article from articles with {author: ["Test Person"]} %} {% for author in article.authors %} {{ author.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('Test Person', $result);
        self::assertNotContains('John Doe', $result);
        self::assertNotContains('Tom', $result);

        $template = '{% gimmelist article from articles if article.authors is empty %} {% for author in article.authors %} {{ author.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEmpty($result);
    }

    public function testFilteringByExcludedAuthors()
    {
        $template = '{% gimmelist article from articles with {author: ["Tom"]} without {author: ["Test Person", "John Doe"]} %} {% for author in article.authors %} {{ author.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('Tom', $result);
        self::assertNotContains('Test Person', $result);
        self::assertNotContains('John Doe', $result);
    }

    public function testFilteringByIncludedAndExcludedAuthors()
    {
        $template = '{% gimmelist article from articles with {author: ["Tom"]} without {author: ["Test Person", "John Doe"]} %} {% for author in article.authors %} {{ author.name }} {% endfor %} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertContains('Tom', $result);
        self::assertNotContains('Test Person', $result);
        self::assertNotContains('John Doe', $result);
    }

    public function testFilteringByExcludedArticles()
    {
        $template = '{% gimme article with {id: 1} %}{% gimmelist article from articles without {article: [article, 2]} %} {{ article.slug }} (id: {{ article.id }}) {% endgimmelist %}{% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' test-article (id: 3)  features (id: 4)  features-client1 (id: 5) ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
