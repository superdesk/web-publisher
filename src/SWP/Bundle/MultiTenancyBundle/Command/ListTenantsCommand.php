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

use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListTenantsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:tenant:list')
            ->setDescription('List all available tenants.')
            ->setDefinition([
                new InputOption('organization', 'o', InputOption::VALUE_REQUIRED, 'Organization code (ex: 123456)', null),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var TenantInterface[] $tenants */
        if (null !== $input->getOption('organization')) {
            $organization = $this->getContainer()->get('swp.repository.organization')->findOneByCode($input->getOption('organization'));
            $tenants = $this->getContainer()->get('swp.repository.tenant')->findBy([
                'organization' => $organization,
            ]);
        } else {
            $tenants = $this->getContainer()->get('swp.repository.tenant')->findAll();
        }

        if (0 === count($tenants)) {
            $output->writeln('<error>There are no tenants defined.</error>');

            return;
        }

        $output->writeln('<info>List of all available tenants:</info>');
        $table = new Table($output);
        $table->setHeaders(['Id', 'Code', 'Name', 'Is active?', 'Created at', 'Organization']);
        foreach ($tenants as $tenant) {
            $table->addRow([
                $tenant->getId(),
                $tenant->getCode(),
                $tenant->getName(),
                $tenant->isEnabled() ? 'yes' : 'no',
                $tenant->getCreatedAt()->format('Y-m-d H:i:s'),
                $tenant->getOrganization()->getName().' (code: '.$tenant->getOrganization()->getCode().')',
            ]);
        }

        $table->render();
    }
}
