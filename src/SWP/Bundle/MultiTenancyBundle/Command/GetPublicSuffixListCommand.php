<?php

namespace SWP\Bundle\MultiTenancyBundle\Command;

use Pdp\Rules;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Cache\CacheInterface;

class GetPublicSuffixListCommand extends Command
{
    protected static $defaultName = 'swp:public-suffix-list:get';

    private ContainerInterface $container;
    private string $suffixListEndpoint;
    private string $suffixListFilename;

    public function __construct(
        ContainerInterface $container,
        string             $suffixListEndpoint,
        string             $suffixListFilename
    )
    {
        $this->container = $container;
        $this->suffixListFilename = $suffixListFilename;
        $this->suffixListEndpoint = $suffixListEndpoint;
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Fetching data from </info> ' . $this->suffixListEndpoint);
        $filesystem = new Filesystem();
        $dir = $this->container->getParameter('kernel.project_dir') . '/data/public-suffix-list/';
        if ($filesystem->exists($dir . $this->suffixListFilename)) {
            $filesystem->remove($dir . $this->suffixListFilename);
        }
        $filesystem->mkdir($dir);
        $filesystem->touch($dir . $this->suffixListFilename);
        $data = file_get_contents($this->suffixListEndpoint);
        $filesystem->dumpFile($dir . $this->suffixListFilename, $data);

        $output->writeln('<info>Data saved into:</info> ' . $dir . $this->suffixListFilename);
        return 0;
    }
}