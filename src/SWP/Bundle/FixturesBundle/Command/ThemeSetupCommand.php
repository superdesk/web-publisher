<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundle\Command;

use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSetupCommand extends ContainerAwareCommand
{
    const DEFAULT_THEME_TITLE = 'DefaultTheme';
    const DEFAULT_THEME_NAME = 'swp/default-theme';
    const THEMES_PATH = '/themes/';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('theme:setup')
            ->setDescription('Sets (copies)/deletes theme(s) for development purposes.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Theme name',
                null
            )
            ->addOption(
                'delete',
                null,
                InputOption::VALUE_NONE,
                'If set, theme will be removed from the application.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'If set, forces to execute an action without confirmation.'
            )
            ->setHelp(
<<<'EOT'
The <info>%command.name%</info> command copies theme to your application themes folder (app/themes):

  <info>%command.full_name%</info>

You can also optionally specify the delete (<info>--delete</info>) option to delete theme by name:

  <info>%command.full_name% <name> --delete</info>

To force an action, you need to add an option: <info>--force</info>:

  <info>%command.full_name% <name> --delete --force</info>

Demo theme can be found in "SWPFixturesBundle/Resources/themes".
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $kernel = $this->getContainer()->get('kernel');
        $name = $input->getArgument('name');
        $helper = $this->getHelper('question');
        $force = true === $input->getOption('force');

        if (null === $name) {
            $name = self::DEFAULT_THEME_TITLE;
        }

        // find default tenant and get its code, use it as a themes path
        $tenantRepository = $this->getContainer()->get('swp.repository.tenant');

        /** @var ThemeAwareTenantInterface $defaultTenant */
        $defaultTenant = $tenantRepository->findOneBy(['name' => TenantInterface::DEFAULT_TENANT_NAME]);

        $this->assertTenantIsFound($defaultTenant);

        $tenantThemeDir = $kernel->getRootDir().self::THEMES_PATH.$defaultTenant->getCode();
        $themeDir = $tenantThemeDir.\DIRECTORY_SEPARATOR.$name;

        try {
            if ($input->getOption('delete')) {
                $question = new ConfirmationQuestion(
                    '<question>This will delete your current theme: "'.$name.'", if exists. Continue with this action? (yes/no)<question> <comment>[yes]</comment> ',
                    true,
                    '/^(y|j)/i'
                );

                if (!$force) {
                    if (!$helper->ask($input, $output, $question)) {
                        return;
                    }
                }

                $fileSystem->remove($themeDir);
                if (!(new \FilesystemIterator($tenantThemeDir))->valid()) {
                    $fileSystem->remove($tenantThemeDir);
                }

                $output->writeln('<info>Theme "'.$name.'" has been deleted successfully!</info>');

                return true;
            }

            $question = new ConfirmationQuestion(
                '<question>This will override your current theme: "'.$name.'", if exists. Continue with this action? (yes/no)<question> <comment>[yes]</comment> ',
                true,
                '/^(y|j)/i'
            );

            if (!$force) {
                if (!$helper->ask($input, $output, $question)) {
                    return;
                }
            }

            // Set theme_name for default tenant if the default theme is being set up
            if (self::DEFAULT_THEME_TITLE === $name) {
                $this->assignDefaultTheme($defaultTenant);
            }

            $fileSystem->mirror(
                $kernel->locateResource('@SWPFixturesBundle/Resources/themes/'.$name),
                $themeDir,
                null,
                ['override' => true, 'delete' => true]
            );

            $output->writeln('<info>Theme "'.$name.'" has been setup successfully!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Theme "'.$name.'" could not be setup!</error>');
            $output->writeln('<error>Stacktrace: '.$e->getMessage().'</error>');
        }
    }

    /**
     * @param ThemeAwareTenantInterface $defaultTenant
     *
     * @throws \Exception if there is no default tenant
     */
    private function assignDefaultTheme(ThemeAwareTenantInterface $defaultTenant)
    {
        // Only assign default theme if no theme has yet been assigned to default tenant
        if (null === $defaultTenant->getThemeName()) {
            $defaultTenant->setThemeName(self::DEFAULT_THEME_NAME);
            $documentManager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
            $documentManager->flush();
        }
    }

    private function assertTenantIsFound(ThemeAwareTenantInterface $defaultTenant)
    {
        if (null === $defaultTenant) {
            throw new TenantNotFoundException('No default tenant found, please first run php app/console swp:tenant:create --default');
        }
    }
}
