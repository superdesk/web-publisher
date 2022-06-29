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

use SWP\Bundle\CoreBundle\Theme\Service\ThemeServiceInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSetupCommand extends Command
{
    protected static $defaultName = 'swp:theme:install';

    /** @var TenantContextInterface  */
    private $tenantContext;

    /** @var TenantRepositoryInterface  */
    private $tenantRepository;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ParameterBagInterface */
    private  $parameterBag;

    /** @var ThemeServiceInterface */
    private $themeService;

    /**
     * @param TenantContextInterface $tenantContext
     * @param TenantRepositoryInterface $tenantRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param ParameterBagInterface $parameterBag
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository,
        EventDispatcherInterface $eventDispatcher,
        ParameterBagInterface $parameterBag,
        ThemeServiceInterface $themeService
    ) {
      $this->tenantContext = $tenantContext;
      $this->tenantRepository = $tenantRepository;
      $this->eventDispatcher = $eventDispatcher;
      $this->parameterBag = $parameterBag;
      $this->themeService = $themeService;
      parent::__construct();
    }


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

            return 1;
        }

        if (!$fileSystem->exists($sourceDir.DIRECTORY_SEPARATOR.'theme.json')) {
            $output->writeln('<error>Source directory doesn\'t contain a theme!</error>');

            return 1;
        }

        $tenantRepository = $this->tenantRepository;
        $tenantContext = $this->tenantContext;
        $eventDispatcher = $this->eventDispatcher;

        $tenant = $tenantRepository->findOneByCode($input->getArgument('tenant'));
        $this->assertTenantIsFound($input->getArgument('tenant'), $tenant);
        $tenantContext->setTenant($tenant);
        $eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);
        $themesDir = $this->parameterBag->get('swp.theme.configuration.default_directory');
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
                return 1;
            }
        }

        try {
            $themeService = $this->themeService;
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
            throw  $e;
            return 1;
        }

        return 0;
    }

    private function assertTenantIsFound(string $tenantCode, ThemeAwareTenantInterface $tenant = null)
    {
        if (null === $tenant) {
            throw new TenantNotFoundException($tenantCode);
        }
    }
}
