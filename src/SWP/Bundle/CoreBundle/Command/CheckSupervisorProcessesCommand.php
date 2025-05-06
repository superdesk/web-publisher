<?php

namespace SWP\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CheckSupervisorProcessesCommand extends Command
{
    protected static $defaultName = 'app:check-supervisor';

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Check if Supervisor processes are running and start them if they are not.')
            ->setHelp(
                'The <info>%command.name%</info> command checks if Supervisor processes are running and start them if they are not.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['supervisorctl', 'status', 'messenger-consume:*']);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Could not retrieve Supervisor processes.</error>');
            return Command::FAILURE;
        }

        $lines = explode(PHP_EOL, trim($process->getOutput()));
        foreach ($lines as $line) {
            $processName = strtok($line, ' ');

            if (!$processName) {
                $output->writeln('<error>Unable to parse process name.</error>');
                continue;
            }

            if (stripos($line, 'RUNNING') === false) {
                $output->writeln("<comment>$processName is NOT running. Starting...</comment>");
                $startProcess = new Process(['supervisorctl', 'start', $processName]);
                $startProcess->run();

                if ($startProcess->isSuccessful()) {
                    $output->writeln("<info>$processName started successfully.</info>");
                } else {
                    $output->writeln("<error>Failed to start $processName</error>");
                }
            } else {
                $output->writeln("<info>$processName is running.</info>");
            }
        }

        return Command::SUCCESS;
    }
}
