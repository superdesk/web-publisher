<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class DownloadThemesFromExternalStorageCommand extends Command
{
    protected static $defaultName = 'swp:theme:download-from-external';

    private $themesDirectory;

    private $themesUrl;

    public function __construct(
        string $themesDirectory,
        string $themesUrl
    ) {
        parent::__construct();
        $this->themesDirectory = $themesDirectory;
        $this->themesUrl = $themesUrl;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Downloads theme from configured storage.')
            ->setHelp(<<<EOT
Location for themes archive can be defined by THEMES_DOWNLOAD_URL env variable. 

Themes must be packed into one compressed file (*.zip). 
Archive can be created with 'zip -r ../themes.zip *' command (called from your themes directory). 

Remember that themes must be located in their tenants directories. Example:

123abc
   theme_1
   theme_2
456def
   theme_3 

EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ('' === $this->themesUrl) {
            $output->writeln('<bg=red;options=bold>Themes archive url is empty.</>');

            return;
        }
        $client = new Client();
        $tempLocation = rtrim(sys_get_temp_dir(), '/').DIRECTORY_SEPARATOR.'themes.zip';
        $client->request('GET', $this->themesUrl, ['sink' => $tempLocation]);

        $filesystem = new Filesystem();

        $zip = new \ZipArchive();
        if (true === $zip->open($tempLocation)) {
            if (!$filesystem->exists($this->themesDirectory)) {
                $filesystem->mkdir($this->themesDirectory);
            }

            $zip->extractTo($this->themesDirectory);
            $zip->close();
        }

        $output->writeln('<bg=green;options=bold>Done.</>');
    }
}
