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

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

class SlideshowLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var LoaderInterface
     */
    private $articleLoader;

    public function setUp(): void
    {
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article_slideshows']);
        $this->twig = $this->getContainer()->get('twig');
        $this->articleLoader = $this->getContainer()->get('swp_template_engine.loader.article');
    }

    public function testLoadSlideshows(): void
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article']);

        $template = '{% gimmelist slideshow from slideshows %} {{ slideshow.code }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow1  slideshow3 ', $result);
    }

    public function testLoadSlideshowByName(): void
    {
        $this->articleLoader->load('article', ['slug' => 'test-news-article-2']);

        $template = '{% gimme slideshow with {name: "slideshow2"} %} {{ slideshow.code }} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow2 ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
