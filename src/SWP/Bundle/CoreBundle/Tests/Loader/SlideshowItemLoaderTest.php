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

namespace SWP\Bundle\CoreBundle\Tests\Loader;

use SWP\Bundle\ContentBundle\Loader\SlideshowLoader;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

class SlideshowItemLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var LoaderInterface
     */
    private $articleLoader;

    /**
     * @var LoaderInterface
     */
    private $slideshowLoader;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article_slideshows']);
        $this->twig = $this->getContainer()->get('twig');
        $this->articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
        $this->slideshowLoader = $this->getContainer()->get(SlideshowLoader::class);
    }

    public function testLoadAllArticleSlideshowsItems(): void
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article']);

        $template = '{% gimmelist slideshowItem from slideshowItems %} {{slideshowItem.slideshow.code}}-{{ url(slideshowItem.articleMedia) }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow1-http://localhost/uploads/swp/123456/media/123456.mp4  slideshow1-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg  slideshow3-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg ', $result);
    }

    public function testLoadAllArticleSlideshowsWithItems()
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article']);

        $template = '{% gimmelist slideshow from slideshows %}{% gimmelist slideshowItem from slideshowItems with { slideshow: slideshow } %} {{slideshowItem.slideshow.code}}-{{ url(slideshowItem.articleMedia) }} {% endgimmelist %}{% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow1-http://localhost/uploads/swp/123456/media/123456.mp4  slideshow1-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg  slideshow3-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg ', $result);
    }

    public function testNotLoadingSlideshowItemsBelongingToAnotherArticle(): void
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article-2']);
        $this->slideshowLoader->load('slideshow', ['name' => 'slideshow1']);

        $template = '{% gimme slideshow with { name: "slideshow1"} %}{% gimmelist slideshowItem from slideshowItems with { slideshow: slideshow } %}{{slideshowItem.slideshow.code}}-{{ url(slideshowItem.articleMedia) }}{% endgimmelist %}{% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals('', $result);
    }

    public function testLoadSlideshowItemsBySlideshowAndCurrentArticle(): void
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article']);
        $this->slideshowLoader->load('slideshow', ['name' => 'slideshow1']);

        $template = '{% gimme slideshow with { name: "slideshow1"} %}{% gimmelist slideshowItem from slideshowItems with { slideshow: slideshow } %}{{slideshowItem.slideshow.code}}-{{ url(slideshowItem.articleMedia) }}{% endgimmelist %}{% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals('slideshow1-http://localhost/uploads/swp/123456/media/123456.mp4slideshow1-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg', $result);

        $this->slideshowLoader->load('slideshow', ['name' => 'slideshow3']);

        $template = '{% gimme slideshow with { name: "slideshow3"} %}{% gimmelist slideshowItem from slideshowItems with { slideshow: slideshow } %}{{slideshowItem.slideshow.code}}-{{ url(slideshowItem.articleMedia) }}{% endgimmelist %}{% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals('slideshow3-http://localhost/uploads/swp/123456/media/12345678987654321a.jpg', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
