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

class RelatedArticleLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant', 'article']);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testRenderingRelatedArticles(): void
    {
        $template = '{% gimmelist relatedArticle from relatedArticles %} {{ relatedArticle.article.title }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' Test news sports article  Test article ', $result);
    }

    private function getRendered($template, $context = []): string
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
