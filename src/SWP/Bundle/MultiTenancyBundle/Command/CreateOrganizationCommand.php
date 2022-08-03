<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancyBundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Command;

use Doctrine\Persistence\ObjectManager;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateOrganizationCommand.
 */
class CreateOrganizationCommand extends Command
{
    protected static $defaultName = 'swp:organization:create';

    /** @var OrganizationRepositoryInterface */
    private $organizationRepository;

    /** @var OrganizationFactoryInterface */
    private  $organizationFactory;

  /**
   * @param OrganizationRepositoryInterface $organizationRepository
   * @param OrganizationFactoryInterface $organizationFactory
   */
  public function __construct( OrganizationRepositoryInterface $organizationRepository, OrganizationFactoryInterface $organizationFactory) {
    $this->organizationRepository = $organizationRepository;
    $this->organizationFactory = $organizationFactory;
    parent::__construct();
  }


  /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:organization:create')
            ->setDescription('Creates a new organization.')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Organization name'),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the organization as a disabled'),
                new InputOption('default', null, InputOption::VALUE_NONE, 'Creates the default organization'),
            ])
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command creates a new organization.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $default = $input->getOption('default');
        $code = null;
        if ($default) {
            $name = OrganizationInterface::DEFAULT_NAME;
            $code = OrganizationInterface::DEFAULT_CODE;
        }

        $organization = $this->getOrganizationRepository()->findOneByName($name);

        if (null !== $organization) {
            throw new \InvalidArgumentException(sprintf('"%s" organization already exists!', $name));
        }

        $organization = $this->createOrganization($name, $input, $code);

        $this->getObjectManager()->persist($organization);
        $this->getObjectManager()->flush();

        $this->sendOutput($output, $organization);

        return 0;
    }

    protected function sendOutput(OutputInterface $output, OrganizationInterface $organization)
    {
        $output->writeln(
            sprintf(
                'Organization <info>%s</info> (code: <info>%s</info>) has been created and <info>%s</info>!',
                $organization->getName(),
                $organization->getCode(),
                $organization->isEnabled() ? 'enabled' : 'disabled'
            )
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $default = $input->getOption('default');
        if (!$default) {
            $this->askAndValidateInteract($input, $output, 'name');
        }
    }

    /**
     * @param string $name
     */
    protected function askAndValidateInteract(InputInterface $input, OutputInterface $output, $name)
    {
        if (!$input->getArgument($name)) {
            $question = new Question(sprintf('<question>Please enter %s:</question>', $name));
            $question->setValidator(function ($argument) use ($name) {
                if (empty($argument)) {
                    throw new \RuntimeException(sprintf('The %s can not be empty', $name));
                }

                return $argument;
            });

            $question->setMaxAttempts(3);

            $argument = $this->getHelper('question')->ask($input, $output, $question);

            $input->setArgument($name, $argument);
        }
    }

    protected function createOrganization(string $name, InputInterface $input, string $code = null): OrganizationInterface
    {
        $organizationFactory = $this->organizationFactory;
        /* @var OrganizationInterface $organization */
        if (null !== $code) {
            $organization = $organizationFactory->create();
            $organization->setCode($code);
        } else {
            $organization = $organizationFactory->createWithCode();
        }

        $organization->setName($name);
        $organization->setEnabled(!$input->getOption('disabled'));

        return $organization;
    }

    /**
     * @return RepositoryInterface
     */
    protected function getObjectManager()
    {
        return $this->organizationRepository;
    }

    /**
     * @return OrganizationRepositoryInterface
     */
    protected function getOrganizationRepository()
    {
        return $this->organizationRepository;
    }
}
