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

class KeywordLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testLoadKeywordBySlug(): void
    {
        $template = '{% gimme keyword with {slug: "big-city"} %} {{ keyword.name }} {{ keyword.slug }} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' Big city big-city ', $result);

        $template = '{% gimme keyword with {slug: "traffic"} %} {{ keyword.name }} {{ keyword.slug }} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' traffic traffic ', $result);
    }

    public function testLoadKeywords(): void
    {
        $template = '{% gimmelist keyword from keywords %} {{ keyword.name }} {{ keyword.slug }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals('', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
