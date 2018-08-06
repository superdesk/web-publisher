<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Loader;

use SWP\Bundle\FixturesBundle\WebTestCase;

class SlideshowLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article_slideshows']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testLoadSlideshows(): void
    {
        $template = '{% gimmelist slideshow from slideshows %} {{ slideshow.code }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow1  slideshow2 ', $result);
    }

    public function testLoadSlideshowByName(): void
    {
        $template = '{% gimme slideshow with {name: "slideshow2"} %} {{ slideshow.code }} {% for item in slideshow.items %} {{ url(item) }} {% endfor %} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' slideshow2  http://localhost/media/12345678987654321a.jpeg  ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
