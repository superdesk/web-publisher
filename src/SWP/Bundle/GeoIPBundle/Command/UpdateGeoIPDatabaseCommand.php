<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Geo IP Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\GeoIPBundle\Command;

use RuntimeException;
use SWP\Component\Archiver\Archiver\GzipArchiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class UpdateGeoIPDatabaseCommand extends Command
{
    protected static $defaultName = 'swp:geoip:db:update';

    /** @var string */
    private $targetDir;

    /** @var string */
    private $databaseUrl;

    /** @var string */
    private $databasePath;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(string $targetDir, string $databaseUrl, string $databasePath, Filesystem $filesystem)
    {
        $this->targetDir = $targetDir;
        $this->databaseUrl = $databaseUrl;
        $this->databasePath = $databasePath;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Downloads and updates the Geo IP database.')
            ->addArgument(
                'url',
                InputArgument::OPTIONAL,
                'GeoIP2 database URL',
                $this->databaseUrl
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $archiver = new GzipArchiver();
        $url = (string) $input->getArgument('url');

        $zipFile = $this->targetDir.'/'.basename($url);

        $this->filesystem->copy($url, $zipFile, true);
        $output->writeln('Database has been downloaded!');

        $isUnArchived = $archiver->unarchive($zipFile, $this->databasePath);

        if (false === $isUnArchived) {
            throw new RuntimeException('Failed to unarchive the database file.');
        }

        $this->filesystem->remove($zipFile);

        $output->writeln('Success!');
    }
}
