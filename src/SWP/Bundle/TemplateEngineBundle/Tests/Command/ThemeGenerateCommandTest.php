<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Command;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Bundle\TemplateEngineBundle\Command\ThemeGenerateCommand;
use Symfony\Component\Filesystem\Filesystem;

class ThemeGenerateCommandTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $this->command = new ThemeGenerateCommand();
        $this->command->setContainer($this->getContainer());
    }

    public function testCommand()
    {
        $fileSystem = new Filesystem();
        $tenantThemeDir = implode(\DIRECTORY_SEPARATOR, [$this->getContainer()->get('kernel')->getRootDir(), ThemeGenerateCommand::THEMES_DIR, '123abc']);
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
