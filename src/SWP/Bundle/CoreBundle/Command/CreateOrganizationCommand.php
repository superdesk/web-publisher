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
use SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand as BaseCreateOrganizationCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOrganizationCommand extends BaseCreateOrganizationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Creates a new organization or updates existing one.')
            ->addOption('secretToken', 's', InputOption::VALUE_REQUIRED, 'Organization secret token');
    }

    /**
     * {@inheritdoc}
     */
    public function createOrganization($name, $input)
    {
        $secretToken = $input->getOption('secretToken');
        /** @var OrganizationInterface $organization */
        $organization = parent::createOrganization($name, $input);
        if ($secretToken) {
            $organization->setSecretToken($secretToken);
        }

        return $organization;
    }

    /**
     * @param OutputInterface       $output
     * @param OrganizationInterface $organization
     */
    protected function sendOutput(OutputInterface $output, $organization)
    {
        $output->writeln(
            sprintf(
                'Organization <info>%s</info> (code: <info>%s</info>%s) has been created and <info>%s</info>!',
                $organization->getName(),
                $organization->getCode(),
                $organization->getSecretToken() ? ', secret token: <info>'.$organization->getSecretToken().'</info>' : '',
                $organization->isEnabled() ? 'enabled' : 'disabled'
            )
        );
    }
}
