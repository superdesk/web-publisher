<?php

namespace SWP\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Hoa\Mime\Mime;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\FileBinaryMimeTypeGuesser;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Mime\MimeTypes;

class TestCommand extends Command
{
    protected static $defaultName = 'swp:test:script';

    private $entityManager;
    private $fs;
    private $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        Filesystem $fs
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->fs = $fs;
    }


    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Command used only for testing new code')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file.')
            ->setHelp(<<<'EOT'
The <info>swp:test:script</info> command is used <info>only</info> for <info>testing</info> newly implemented code.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $this->container->getParameter('kernel.project_dir');
        $path = $dir . '/' . $input->getArgument('path');

        if (!file_exists($path)) {
            throw new \InvalidArgumentException('File ' . $path . ' doe not exist.');
        }

        $output->writeln('<info>Checking file:</info> ' . $path);

        $mimType = new FileBinaryMimeTypeGuesser();
        $output->writeln('<info>FileBinaryMimeTypeGuesser:</info> ' . $mimType->guessMimeType($path));

        $mimType = new FileinfoMimeTypeGuesser();
        $output->writeln('<info>FileinfoMimeTypeGuesser:</info> ' . $mimType->guessMimeType($path));

        $mim = MimeTypes::getDefault()->guessMimeType($path);
        $output->writeln('<info>Default Symfony MimType:</info> ' . $mim);


        $mimByExtension = MimeTypes::getDefault()->getMimeTypes($ext = pathinfo($path, PATHINFO_EXTENSION));
        $output->writeln('<info>MimType by extension:</info> ' . implode(',', $mimByExtension));


        $mimExtension = MimeTypes::getDefault()->getExtensions($mimByExtension[0]);
        $output->writeln('<info>Extension of mim types:</info> ' . $mimByExtension[0] . ' <info>is:</info> ' . implode(',', $mimExtension));

        $output->writeln('--------------- My ----------------');

        $output->writeln('<info>My MimTypes checker:</info> ' . \SWP\Bundle\CoreBundle\Util\MimeTypeHelper::getByPath($path));
        $output->writeln('<info>My Extensions by MimTypes checker:</info> ' . \SWP\Bundle\CoreBundle\Util\MimeTypeHelper::getExtensionByMimeType('application/json'));
        $output->writeln('<info>My Extensions by MimTypes checker:</info> ' . \SWP\Bundle\CoreBundle\Util\MimeTypeHelper::getExtensionByMimeType('application/javascript'));
        $output->writeln('<info>My Extensions by MimTypes checker:</info> ' . \SWP\Bundle\CoreBundle\Util\MimeTypeHelper::getExtensionByMimeType('image/png'));
        $output->writeln('<info>My Extensions by MimTypes checker:</info> ' . \SWP\Bundle\CoreBundle\Util\MimeTypeHelper::getExtensionByMimeType('image/jpeg'));

        return 0;
    }
}