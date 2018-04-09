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

namespace SWP\Bundle\CoreBundle\Command;

use SWP\Bundle\CoreBundle\Model\OrganizationInterface;
use SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUpdateOrganizationCommand extends CreateOrganizationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Creates a new organization or updates existing one.')
            ->addOption('secretToken', 's', InputOption::VALUE_REQUIRED, 'Organization secret token')
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Update existing organization');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $disabled = $input->getOption('disabled');
        $secretToken = $input->getOption('secretToken');
        $update = $input->getOption('update');
        $default = $input->getOption('default');

        if ($default) {
            $name = OrganizationInterface::DEFAULT_NAME;
        }

        /** @var OrganizationInterface $organization */
        $organization = $this->getOrganizationRepository()->findOneByName($name);

        if (null !== $organization && $update) {
            $this->updateOrganization($organization, $disabled, $secretToken);
            $message = sprintf(
                'Organization <info>%s</info> (code: <info>%s</info>) has been updated and is <info>%s</info>!',
                $name,
                $organization->getCode(),
                $organization->isEnabled() ? 'enabled' : 'disabled'
            );
        } elseif (null !== $organization) {
            throw new \InvalidArgumentException(sprintf('"%s" organization already exists!', $name));
        } else {
            $organization = $this->createOrganization($name, $disabled);
            $organization->setSecretToken($secretToken);
            $message = sprintf(
                'Organization <info>%s</info> (code: <info>%s</info>) has been created and <info>%s</info>!',
                $name,
                $organization->getCode(),
                $organization->isEnabled() ? 'enabled' : 'disabled'
            );
        }

        $this->getObjectManager()->persist($organization);
        $this->getObjectManager()->flush();
        $output->writeln($message);
    }

    /**
     * @param OrganizationInterface $organization
     * @param bool                  $disabled
     * @param null|string           $secretToken
     */
    protected function updateOrganization(OrganizationInterface $organization, bool $disabled, ?string $secretToken)
    {
        $organization->setEnabled(!$disabled);
        if ($secretToken) {
            $organization->setSecretToken($secretToken);
        }
    }
}
