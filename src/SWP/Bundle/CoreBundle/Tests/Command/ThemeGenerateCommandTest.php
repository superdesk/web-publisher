<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
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

use SWP\Bundle\CoreBundle\Command\ThemeGenerateCommand;
use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ThemeGenerateCommandTest extends WebTestCase
{

    private ?ThemeGenerateCommand $command;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);

        $this->command = new ThemeGenerateCommand(
            $this->getContainer()->get(ParameterBagInterface::class),
            $this->getContainer()->get(OrganizationRepositoryInterface::class),
        );
    }

    public function testCommand()
    {
        $fileSystem = new Filesystem();
        $themesDir = $this->getContainer()->getParameter('swp.theme.configuration.default_directory');
        $tenantThemeDir = implode(\DIRECTORY_SEPARATOR, [$themesDir, '123abc']);
        $tenantThemeDirExisted = $fileSystem->exists($tenantThemeDir);

        try {
            $themeName = 'booyaka';
            $themeDir = $tenantThemeDir.\DIRECTORY_SEPARATOR.$themeName;
            $this->assertFalse($fileSystem->exists($themeDir), 'Theme already exists');

            $result = $this->runCommand('theme:generate', ['organizationName' => 'default', 'themeName' => 'booyaka'], true);
            $this->assertContains('Theme booyaka has been generated successfully', $result);

            $this->assertTrue($fileSystem->exists($themeDir), 'Theme not created');

            $result = $this->runCommand('theme:generate', ['organizationName' => 'default', 'themeName' => 'booyaka'], true);
            $this->assertContains('Theme booyaka already exists!', $result);
        } catch (\Exception $e) {
        }

        if ($tenantThemeDirExisted) {
            $fileSystem->remove($themeDir);
        } else {
            $fileSystem->remove($tenantThemeDir);
        }
    }
}
