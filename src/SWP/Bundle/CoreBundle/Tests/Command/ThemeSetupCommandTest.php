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

namespace SWP\Bundle\CoreBundle\Tests\Command;

use SWP\Bundle\CoreBundle\Command\ThemeSetupCommand;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSetupCommandTest extends WebTestCase
{
    private $commandTester;

    private $command;

    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);

        $this->command = self::createCommand();
        $this->commandTester = $this->createCommandTester();
    }

    protected static function createCommand()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ThemeSetupCommand());

        return $application->find('swp:theme:install');
    }

    protected function createCommandTester()
    {
        $command = self::createCommand();

        return new CommandTester($command);
    }

    /**
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install',
                '--force' => true,
            ]
        );

        self::assertContains('Theme has been installed successfully!', $this->commandTester->getDisplay());
    }

    public function testExecuteWhenDirectoryNotValid()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => 'fake/dir',
                '--force' => true,
            ]
        );

        self::assertContains('Directory "fake/dir" does not exist or it is not a directory!', $this->commandTester->getDisplay());
    }

    public function testExecuteWhenFailure()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => '/',
                '--force' => true,
            ]
        );

        self::assertContains('Source directory doesn\'t contain a theme!', $this->commandTester->getDisplay());
    }

    public function testExecuteWithActivation()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install',
                '--force' => true,
                '--activate' => true,
            ]
        );

        self::assertContains('Theme has been installed successfully!', $this->commandTester->getDisplay());
        self::assertContains('Theme was activated!', $this->commandTester->getDisplay());

        $client = self::createClient();
        $router = $this->getContainer()->get('router');
        $client->request('GET', $router->generate('swp_api_core_get_tenant', ['code' => '123abc']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('swp/test-theme-install', $content['theme_name']);
    }

    public function testExecuteWithBrokenThemeConfiguration()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install_with_broken_theme_json',
                '--force' => true,
            ]
        );

        self::assertContains('Theme could not be installed, files are reverted to previous version!', $this->commandTester->getDisplay());
    }

    public function testExecuteWithActivationAndDataGeneration()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install_with_generated_data',
                '--processGeneratedData' => true,
                '--force' => true,
                '--activate' => true,
            ]
        );

        self::assertContains('Theme has been installed successfully!', $this->commandTester->getDisplay());
        self::assertContains('Theme was activated!', $this->commandTester->getDisplay());

        $client = self::createClient();
        $router = $this->getContainer()->get('router');
        $client->request('GET', $router->generate('swp_api_content_show_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertIsArray($content['feature_media']);
        self::assertCount(1, $content['media']);
        self::assertCount(3, $content['media'][0]['renditions']);
        self::assertNotNull($content['route']);
        self::assertNotNull($content['article_statistics']);

        $client->request('GET', $router->generate('swp_api_content_list_lists'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);

        $client->request('GET', $router->generate('swp_api_content_list_routes'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(4, $content['_embedded']['_items']);

        $client->request('GET', $router->generate('swp_api_core_list_menu'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $content['_embedded']['_items']);
        self::assertCount(2, $content['_embedded']['_items'][0]['children']);
        self::assertCount(2, $content['_embedded']['_items'][1]['children']);
    }

    /**
     * @expectedException \SWP\Component\MultiTenancy\Exception\TenantNotFoundException
     */
    public function testExecuteWhenTenantNotFound()
    {
        $this->commandTester->execute(
            [
                'tenant' => '111',
                'theme_dir' => __DIR__.'/../Fixtures/themes_to_be_installed/theme_test_install',
                '--force' => true,
            ]
        );
    }

    public static function tearDownAfterClass(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../Fixtures/themes/123abc/theme_test_install');
        $filesystem->remove(__DIR__.'/../Fixtures/themes/123abc/theme_test_install_with_generated_data');
    }
}
