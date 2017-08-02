<?php

declare(strict_types=1);

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

namespace SWP\Bundle\CoreBundle\Tests\Fragment;

use Superdesk\ContentApiSdk\Exception\ClientException;
use SWP\Bundle\CoreBundle\Fragment\ExternalFragmentRenderer;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ExternalFragmentRendererTest extends WebTestCase
{
    protected $renderer;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->renderer = new ExternalFragmentRenderer(
            $this->getContainer()->get('kernel'),
            $this->getContainer()->get('event_dispatcher')
        );
    }

    public function testRendering()
    {
        $content = json_decode($this->renderer->render('localhost:3000/esi_fragment', new Request())->getContent(), true);
        self::assertEquals(['content' => 'some content'], $content);
    }

    public function testRenderingAlternativeContent()
    {
        $content = json_decode($this->renderer->render('localhost:3001/404', new Request(), [
            'alt' => 'localhost:3000/esi_fragment',
        ])->getContent(), true);
        self::assertEquals(['content' => 'some content'], $content);
    }

    public function testRenderingWithErrors()
    {
        $this->expectException(ClientException::class);
        $content = json_decode($this->renderer->render('localhost:3001/404', new Request(), [
            'ignore_errors' => false,
        ])->getContent(), true);
        self::assertEquals(['content' => 'some content'], $content);
    }
}
