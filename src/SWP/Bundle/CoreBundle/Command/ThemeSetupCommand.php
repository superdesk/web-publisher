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

namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSetupCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:theme:install')
            ->setDescription('Installs theme.')
            ->addArgument(
                'tenant',
                InputArgument::REQUIRED,
                'Tenant code. For this tenant the theme will be installed.'
            )
            ->addArgument(
                'theme_dir',
                InputArgument::REQUIRED,
                'Path to theme you want to install.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'If set, forces to execute an action without confirmation.'
            )
            ->addOption(
                'activate',
                'a',
                InputOption::VALUE_NONE,
                'If set, theme will be activated in tenant.'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command installs your custom theme for given tenant:

  <info>%command.full_name% <tenant> <theme_dir></info>

You need specify the directory (<comment>theme_dir</comment>) argument to install 
theme from any directory:

  <info>%command.full_name% <tenant> /dir/to/theme
  
Once executed, it will create directory <comment>app/themes/<tenant></comment>
where <comment><tenant></comment> is the tenant code you typed in the first argument.

To force an action, you need to add an option: <info>--force</info>:

  <info>%command.full_name% <tenant> <theme_dir> --force</info>

To activate this theme in tenant, you need to add and option <info>--activate</info>:
  <info>%command.full_name% <tenant> <theme_dir> --activate</info>

Theme installation will generated declared in theme config elements 
like: routes, articles, menus, widgets, content lists and containers
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $sourceDir = $input->getArgument('theme_dir');
        if (!$fileSystem->exists($sourceDir) || !is_dir($sourceDir)) {
            $output->writeln(sprintf('<error>Directory "%s" does not exist or it is not a directory!</error>', $sourceDir));

            return;
        }

        if (!$fileSystem->exists($sourceDir.DIRECTORY_SEPARATOR.'theme.json')) {
            $output->writeln(sprintf('<error>Source directory doesn\'t contain a theme!</error>', $sourceDir));

            return;
        }

        $container = $this->getContainer();
        $tenantRepository = $container->get('swp.repository.tenant');
        $tenantContext = $container->get('swp_multi_tenancy.tenant_context');
        $eventDispatcher = $container->get('event_dispatcher');
        $revisionListener = $container->get('swp_core.listener.tenant_revision');

        $tenant = $tenantRepository->findOneByCode($input->getArgument('tenant'));
        $this->assertTenantIsFound($input->getArgument('tenant'), $tenant);
        $tenantContext->setTenant($tenant);
        $revisionListener->setRevisions();
        $eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        $themeInstaller = $container->get('swp_core.installer.theme');

        $force = true === $input->getOption('force');
        $activate = true === $input->getOption('activate');
        $themesDir = $container->getParameter('swp.theme.configuration.default_directory');
        $themeDir = $themesDir.\DIRECTORY_SEPARATOR.$tenant->getCode().\DIRECTORY_SEPARATOR.basename($sourceDir);
        $backupThemeDir = $themesDir.\DIRECTORY_SEPARATOR.'backup'.\DIRECTORY_SEPARATOR.$tenant->getCode().\DIRECTORY_SEPARATOR.basename($sourceDir).'_previous';

        try {
            if ($fileSystem->exists($themeDir)) {
                $fileSystem->rename($themeDir, $backupThemeDir, true);
            }

            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                '<question>This will override your current theme. Continue with this action? (yes/no)<question> <comment>[yes]</comment> ',
                true,
                '/^(y|j)/i'
            );

            if (!$force) {
                if (!$helper->ask($input, $output, $question)) {
                    return;
                }
            }

            $themeInstaller->install(null, $sourceDir, $themeDir);
            /** @var ReloadableThemeRepositoryInterface $themeRepository */
            $themeRepository = $container->get('sylius.repository.theme');
            $themeRepository->reloadThemes();
            $output->writeln('<info>Theme has been installed successfully!</info>');
            if (file_exists($themeDir.\DIRECTORY_SEPARATOR.'theme.json')) {
                $output->writeln('<info>Persisting theme required data...</info>');
                $theme = $container->get('sylius.context.theme')->getTheme();
                $requiredDataProcessor = $container->get('swp_core.processor.theme.required_data');
                $requiredDataProcessor->processTheme($theme);
                $output->writeln('<info>Theme required data was persisted successfully!</info>');

                $themeConfig = json_decode(file_get_contents($themeDir.\DIRECTORY_SEPARATOR.'theme.json'), true);
                $themeName = $themeConfig['name'];
                $tenant->setThemeName($themeName);
                if ($activate) {
                    $tenantRepository->flush();
                    $output->writeln('<info>Theme was activated!</info>');
                }
            }
        } catch (\Exception $e) {
            $fileSystem->remove($themeDir);
            $fileSystem->rename($backupThemeDir, $themeDir);

            $output->writeln('<error>Theme could not be installed, files are reverted to previous verion!</error>');
            $output->writeln('<error>Error message: '.$e->getMessage().'</error>');
        }

        if ($fileSystem->exists($backupThemeDir)) {
            $fileSystem->remove($backupThemeDir);
        }
    }

    /**
     * @param string                         $tenantCode
     * @param ThemeAwareTenantInterface|null $tenant
     */
    private function assertTenantIsFound(string $tenantCode, ThemeAwareTenantInterface $tenant = null)
    {
        if (null === $tenant) {
            throw new TenantNotFoundException($tenantCode);
        }
    }
}
