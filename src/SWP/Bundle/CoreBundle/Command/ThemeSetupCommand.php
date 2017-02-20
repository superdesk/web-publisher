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
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $helper = $this->getHelper('question');
        $force = true === $input->getOption('force');

        /** @var ThemeAwareTenantInterface $tenant */
        $tenant = $this->getContainer()->get('swp.repository.tenant')
            ->findOneByCode($input->getArgument('tenant'));

        $sourceDir = $input->getArgument('theme_dir');

        $this->assertTenantIsFound($input->getArgument('tenant'), $tenant);

        if (!$fileSystem->exists($sourceDir) || !is_dir($sourceDir)) {
            $output->writeln(sprintf('<error>Directory "%s" does not exist or it is not a directory!</error>', $sourceDir));

            return;
        }

        $themesDir = $this->getContainer()->getParameter('swp.theme.configuration.default_directory');
        $tenantThemeDir = $themesDir.\DIRECTORY_SEPARATOR.$tenant->getCode();
        $themeDir = $tenantThemeDir.\DIRECTORY_SEPARATOR.basename($sourceDir);

        try {
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

            $fileSystem->mirror($sourceDir, $themeDir, null, ['override' => true, 'delete' => true]);

            $output->writeln('<info>Theme has been installed successfully!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Theme could not be installed!</error>');
            $output->writeln('<error>Stacktrace: '.$e->getMessage().'</error>');
        }
    }

    private function assertTenantIsFound(string $tenantCode, ThemeAwareTenantInterface $tenant = null)
    {
        if (null === $tenant) {
            throw new TenantNotFoundException($tenantCode);
        }
    }
}
