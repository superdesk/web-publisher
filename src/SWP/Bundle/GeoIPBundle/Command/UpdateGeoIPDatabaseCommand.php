<?php

declare(strict_types=1);

namespace SWP\Bundle\GeoIPBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateGeoIPDatabaseCommand extends Command
{
    protected static $defaultName = 'swp:geoip:db:update';

    protected function configure(): void
    {
        $this
            ->setDescription('Downloads and updates the Geo IP database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Whoa!');
    }
}
