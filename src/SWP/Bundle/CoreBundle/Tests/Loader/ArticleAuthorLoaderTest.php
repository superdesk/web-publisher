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
    public function setUp(): void
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
        self::assertEquals(' Tom  Test Person  John Doe  John Doe Second  Test Person ', $result);
    }

    public function testRenderingArticleAuthorsAvatars()
    {
        $template = '{% gimmelist author from authors %} {{ url(author.avatar) }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' http://localhost/uploads/swp/123456/authors/tom.jpg  http://localhost/uploads/swp/123456/authors/test-person.jpg  http://localhost/uploads/swp/123456/authors/john-doe.jpg  http://localhost/uploads/swp/123456/authors/john-doe-second.jpg  http://localhost/uploads/swp/123456/authors/test-person.jpg ', $result);
    }

    public function testRenderingArticleAuthorsOrdered()
    {
        $template = '{% gimmelist author from authors|order("name", "asc") %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' John Doe  John Doe Second  Test Person  Test Person  Tom ', $result);

        $template = '{% gimmelist author from authors|order("name", "desc") %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom  Test Person  Test Person  John Doe Second  John Doe ', $result);
    }

    public function testRenderingArticleAuthorsWith()
    {
        $template = '{% gimmelist author from authors with {jobtitle: {name: "quality check"}} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);

        $template = '{% gimmelist author from authors with {role: ["Writer"]} %} {{ author.name }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom  Test Person  John Doe  John Doe Second  Test Person ', $result);
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

    public function testLoadAuthorBySlug()
    {
        $template = '{% gimme author with {slug: "tom"} %} {{ author.name }} {{ author.slug }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom tom ', $result);

        $template = '{% gimme author with {slug: "john-doe"} %} {{ author.name }} {{ author.slug }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' John Doe john-doe ', $result);
    }

    public function testRenderingAuthorSocialProfiles()
    {
        $template = '{% gimmelist author from authors %} {{ author.name }} {{ author.twitter }} {{ author.facebook }} {{ author.instagram }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' Tom @superdeskman superdeskman superdeskman  Test Person @superdeskman superdeskman superdeskman  John Doe @superdeskman superdeskman superdeskman  John Doe Second @superdeskman superdeskman superdeskman  Test Person @superdeskman superdeskman superdeskman ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
