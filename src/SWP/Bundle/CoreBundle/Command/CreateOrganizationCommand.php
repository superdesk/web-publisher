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

use SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand as BaseCreateOrganizationCommand;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOrganizationCommand extends BaseCreateOrganizationCommand
{
    protected static $defaultName = 'swp:organization:create';

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

    public function createOrganization(string $name, InputInterface $input, string $code = null): OrganizationInterface
    {
        $secretToken = $input->getOption('secretToken');

        $organization = parent::createOrganization($name, $input, $code);
        if ($secretToken) {
            $organization->setSecretToken($secretToken);
        }

        return $organization;
    }

    protected function sendOutput(OutputInterface $output, OrganizationInterface $organization)
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
