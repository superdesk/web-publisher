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
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

class ContentListLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * SetUp test.
     */
    public function setUp()
    {
        self::bootKernel();

        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles(
            [
                '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            ],
            true
        );

        $this->twig = $this->getContainer()->get('twig');
        $this->getContainer()->get('event_dispatcher')->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);
    }

    public function testLoadListByName()
    {
        $template = '{% gimme contentList with { contentListName: "List1"} %} {{ contentList.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' List1 ', $result);

        $template = '{% gimme contentList with { contentListName: "List12"} %} {{ contentList.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals('', $result);
    }

    public function testLoadListById()
    {
        $template = '{% gimme contentList with { contentListId: 1} %} {{ contentList.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' List1 ', $result);

        $template = '{% gimme contentList with { contentListId: 2} %} {{ contentList.name }} {% endgimme %}';
        $result = $this->getRendered($template);
        self::assertEquals(' List2 ', $result);

        $template = '{% gimme contentList with { contentListId: 3} %} {{ contentList.name }} {% endgimme %}';
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
