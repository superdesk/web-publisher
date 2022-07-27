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

class DynamicArticleContentListsLoadingInMetaTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);
        $this->twig = $this->getContainer()->get('twig');
    }

    public function testFetchingContentListsFromArticleMeta()
    {
        $template = '{% gimme article with {id: 1} %}{% for contentList in article.contentLists %} {{ contentList.name }} {% endfor %}{% endgimme %}';
        $result = $this->getRendered($template);

        self::assertContains('List1', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
