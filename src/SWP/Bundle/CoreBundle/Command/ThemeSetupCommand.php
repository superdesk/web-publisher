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
    protected static $defaultName = 'swp:theme:install';

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
            ->addOption(
                'processGeneratedData',
                'p',
                InputOption::VALUE_NONE,
                'If set, theme installer will process generated data defined in theme config.'
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
  
If option <info>--processGeneratedData</info> will be passed theme installator will 
generate declared in theme config elements like: routes, articles, menus andcontent lists
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

        if (!$fileSystem->exists($sourceDir) || !\is_dir($sourceDir)) {
            $output->writeln(sprintf('<error>Directory "%s" does not exist or it is not a directory!</error>', $sourceDir));

            return;
        }

        if (!$fileSystem->exists($sourceDir.DIRECTORY_SEPARATOR.'theme.json')) {
            $output->writeln('<error>Source directory doesn\'t contain a theme!</error>');

            return;
        }

        $container = $this->getContainer();
        $tenantRepository = $container->get('swp.repository.tenant');
        $tenantContext = $container->get('swp_multi_tenancy.tenant_context');
        $eventDispatcher = $container->get('event_dispatcher');

        $tenant = $tenantRepository->findOneByCode($input->getArgument('tenant'));
        $this->assertTenantIsFound($input->getArgument('tenant'), $tenant);
        $tenantContext->setTenant($tenant);
        $eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        $themesDir = $container->getParameter('swp.theme.configuration.default_directory');
        $themeDir = $themesDir.\DIRECTORY_SEPARATOR.$tenant->getCode().\DIRECTORY_SEPARATOR.basename($sourceDir);

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            '<question>This will override your current theme. Continue with this action? (yes/no)<question> <comment>[yes]</comment> ',
            true,
            '/^(y|j)/i'
        );

        if (!$input->getOption('force')) {
            $answer = $helper->ask($input, $output, $question);
            if (!$answer) {
                return;
            }
        }

        try {
            $themeService = $container->get('swp_core.service.theme');
            $installationResult = $themeService->installAndProcessGeneratedData(
                $sourceDir,
                $themeDir,
                $input->getOption('processGeneratedData'),
                $input->getOption('activate')
            );

            foreach ($installationResult as $message) {
                $output->writeln('<info>'.$message.'</info>');
            }
        } catch (\Throwable $e) {
            $output->writeln('<error>Theme could not be installed, files are reverted to previous version!</error>');
            $output->writeln('<error>Error message: '.$e->getMessage().'</error>');
        }
    }

    private function assertTenantIsFound(string $tenantCode, ThemeAwareTenantInterface $tenant = null)
    {
        if (null === $tenant) {
            throw new TenantNotFoundException($tenantCode);
        }
    }
}
