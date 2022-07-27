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

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

final class ThemeLogoTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);

        $this->router = $this->getContainer()->get('router');
        $this->twig = $this->getContainer()->get('twig');
        $this->settingsManager = $this->getContainer()->get('swp_settings.manager.settings');
    }

    public function testThemeLogoUpload()
    {
        $client = static::createClient();

        $client->request('GET', $this->router->generate('swp_api_theme_settings_list'));

        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('theme_logo', $data[0]['name']);
        self::assertEquals('', $data[0]['value']);

        $template = '{{ themeLogo(asset(\'theme/logo.png\')) }}';
        $result = $this->getRendered($template);

        self::assertContains('/bundles/_themes/swp/test-theme@123abc/logo.png', $result);

        $fileName = realpath(__DIR__.'/../Fixtures/logo.png');

        $client->request('POST', $this->router->generate('swp_api_upload_theme_logo'), [],[
            'logo' => new UploadedFile($fileName, 'logo.png', 'image/png', null, true),
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        // Test fix - set it to clear tests settings manager instance internal cache.
        $this->settingsManager->set('first_setting', null);

        $client->request('GET', $this->router->generate('swp_api_theme_settings_list'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('theme_logo', $data[0]['name']);
        self::assertNotEquals('', $data[0]['value']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $route = $this->router->generate('swp_theme_logo_get', ['id' => $data[0]['value']]);
        $client->request('GET', $route);
        self::assertArrayHasKey('content-disposition', $client->getResponse()->headers->all());
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $template = '{{ themeLogo(asset(\'theme/logo.png\')) }}';
        $result = $this->getRendered($template);
        self::assertContains(ltrim($route, '/'), $result);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
