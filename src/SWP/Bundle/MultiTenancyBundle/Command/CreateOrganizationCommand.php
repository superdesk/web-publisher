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

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateOrganizationCommand.
 */
class CreateOrganizationCommand extends ContainerAwareCommand
{
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
        if ($default) {
            $name = OrganizationInterface::DEFAULT_NAME;
        }

        $organization = $this->getOrganizationRepository()->findOneByName($name);

        if (null !== $organization) {
            throw new \InvalidArgumentException(sprintf('"%s" organization already exists!', $name));
        }

        $organization = $this->createOrganization($name, $input);

        $this->getObjectManager()->persist($organization);
        $this->getObjectManager()->flush();

        $this->sendOutput($output, $organization);
    }

    /**
     * @param OutputInterface       $output
     * @param OrganizationInterface $organization
     */
    protected function sendOutput(OutputInterface $output, $organization)
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $default = $input->getOption('default');
        if (!$default) {
            $this->askAndValidateInteract($input, $output, 'name');
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $name
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

    /**
     * @param string $name
     * @param bool   $disabled
     *
     * @return OrganizationInterface
     */
    protected function createOrganization($name, $input)
    {
        $organizationFactory = $this->getContainer()->get('swp.factory.organization');
        /** @var OrganizationInterface $organization */
        $organization = $organizationFactory->createWithCode();
        $organization->setName($name);
        $organization->setEnabled(!$input->getOption('disabled'));

        return $organization;
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getContainer()->get('swp.object_manager.organization');
    }

    /**
     * @return OrganizationRepositoryInterface
     */
    protected function getOrganizationRepository()
    {
        return $this->getContainer()->get('swp.repository.organization');
    }
}
