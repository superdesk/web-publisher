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
        $url = (string) $input->getArgument('url');
        $zipFile = $this->targetDir.'/GeoLite2.tar.gz';
        $tarFile = $this->targetDir.'/GeoLite2.tar';
        $tempUntar = $this->targetDir.'/GeoLite2';
        $this->filesystem->remove([$tempUntar, $tarFile]);

        $this->filesystem->copy($url, $zipFile, true);
        $output->writeln('GeoLite2 database has been downloaded!');

        $zip = new \PharData($zipFile);
        $tar = $zip->decompress();
        $output->writeln('De-compression has been completed!');

        $tar->extractTo($tempUntar);

        $output->writeln("Gzip extracted to $tempUntar!");

        $database = '';
        $files = glob(sprintf('%s/**/*.mmdb', $tempUntar)) ?: [];
        foreach ($files as $file) {
            if (preg_match('/(?<database>[^\/]+)_(?<year>\d{4})(?<month>\d{2})(?<day>\d{2})/', $file, $match)) {
                $database = $file;
            }
        }

        if (!$database) {
            throw new \RuntimeException('GeoLite2 database not found in the gzip file.');
        }

        $this->filesystem->copy($database, $this->databasePath, true);
        $this->filesystem->remove([$zipFile, $tempUntar, $tarFile]);
        $output->writeln("GeoLite2 database has been copied to $this->databasePath!");
    }
}
