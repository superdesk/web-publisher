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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ThemeSetupCommand extends ContainerAwareCommand
{
    const DEFAULT_THEME_NAME = 'DefaultTheme';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('theme:setup')
            ->setDescription('Sets (copies) demo theme for development purposes.')
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
The <info>theme:setup</info> command copies theme to your application themes folder (app/themes):

  <info>./app/console theme:setup</info>

You can also optionally specify the delete (<info>--delete</info>) option to delete theme by name:

  <info>./app/console theme:setup <name> --delete</info>

To force an action, you need to add an option: <info>--force</info>:

  <info>./app/console theme:setup <name> --delete --force</info>

Demo theme can be found in "SWPFixturesBundle/Resources/themes".
EOT
            )
        ;
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

        if (!$name) {
            $name = self::DEFAULT_THEME_NAME;
        }

        try {
            if ($input->getOption('delete')) {
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

                $fileSystem->remove($kernel->getRootDir().'/themes/'.$name);

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

            $fileSystem->mirror(
                $kernel->locateResource('@SWPFixturesBundle/Resources/themes/'.$name),
                $kernel->getRootDir().'/themes/'.$name,
                null,
                ['override' => true, 'delete' => true]
            );

            $output->writeln('<info>Theme "'.$name.'" has been setup successfully!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Theme "'.$name.'" could not be setup!</error>');
            $output->writeln('<error>Stacktrace: '.$e->getMessage().'</error>');
        }
    }
}
