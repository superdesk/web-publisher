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
use Symfony\Component\Routing\RouterInterface;

class ContentListItemLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SWP\Bundle\FixturesBundle\WebTestCase
     */
    private $client;

    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->twig = $this->getContainer()->get('twig');
        $this->router = $this->getContainer()->get('router');
        $this->client = static::createClient();
    }

    public function testFetchingContentListItems()
    {
        $template = '{% gimmelist item from contentListItems with { contentListName: "List1"} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true":"false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article1-0-true  article3-2-true  article2-1-false  article4-3-false ', $result);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_update_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]), [
            'content_list_item' => [
                'sticky' => true,
            ],
        ]);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->getContainer()->get('doctrine')->getManager()->clear();

        $result = $this->getRendered($template);
        self::assertEquals(' article1-0-true  article2-1-true  article3-2-true  article4-3-false ', $result);
    }

    public function testFetchingStickyItems()
    {
        $this->client->request('PATCH', $this->router->generate('swp_api_core_update_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]), [
            'content_list_item' => [
                'sticky' => true,
            ],
        ]);
        $this->getContainer()->get('doctrine')->getManager()->clear();

        $template = '{% gimmelist item from contentListItems with { contentListName: "List1", sticky: true} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true" : "false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article1-0-true  article2-1-true  article3-2-true ', $result);

        $template = '{% gimmelist item from contentListItems with { contentListName: "List1", sticky: true} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true" : "false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article1-0-true  article2-1-true  article3-2-true ', $result);

        $template = '{% gimmelist item from contentListItems with { contentListName: "List1", sticky: false} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true" : "false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article4-3-false ', $result);
    }

    public function testPagination()
    {
        $template = '{% gimmelist item from contentListItems|start(0)|limit(3) with { contentListName: "List1"} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true":"false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article1-0-true  article3-2-true  article2-1-false ', $result);

        $template = '{% gimmelist item from contentListItems|start(3)|limit(3) with { contentListName: "List1"} %} {{ item.content.title }}-{{ item.position}}-{{ item.sticky ? "true":"false" }} {% endgimmelist %}';
        $result = $this->getRendered($template);
        self::assertEquals(' article4-3-false ', $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
