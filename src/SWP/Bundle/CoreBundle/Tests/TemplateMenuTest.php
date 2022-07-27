<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace src\SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class TemplateMenuTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testMenuRenderWhenMenuNotFound()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'menutest',
                'type' => 'content',
                'templateName' => 'menu_template.html.twig',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('GET', '/menutest');
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        self::assertContains('<div></div>', $content);
    }
}
