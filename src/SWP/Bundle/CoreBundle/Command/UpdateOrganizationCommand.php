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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrganizationCommand extends CreateOrganizationCommand
{
    protected static $defaultName = 'swp:organization:update';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:organization:update')
            ->setDescription('Updates existing organization.')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Organization name'),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the organization as a disabled'),
                new InputOption('secretToken', null, InputOption::VALUE_REQUIRED, 'Organization secret token'),
            ])
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command updates existing organization.
EOT
            );
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

        /** @var OrganizationInterface $organization */
        $organization = $this->getOrganizationRepository()->findOneByName($name);
        if (null !== $organization) {
            $this->updateOrganization($organization, $disabled, $secretToken);
        } else {
            throw new \InvalidArgumentException(sprintf('"%s" organization don\'t exists!', $name));
        }

        $this->getObjectManager()->persist($organization);
        $this->getObjectManager()->flush();
        $this->sendOutput($output, $organization);
        return 0;
    }

    /**
     * @param OutputInterface       $output
     * @param OrganizationInterface $organization
     */
    protected function sendOutput(OutputInterface $output, $organization)
    {
        $output->writeln(
            sprintf(
                'Organization <info>%s</info> (code: <info>%s</info>%s) has been updated and is <info>%s</info>!',
                $organization->getName(),
                $organization->getCode(),
                $organization->getSecretToken() ? ', secret token: <info>'.$organization->getSecretToken().'</info>' : '',
                $organization->isEnabled() ? 'enabled' : 'disabled'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->askAndValidateInteract($input, $output, 'name');
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
