<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancyBundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Command;

use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListOrganizationsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:organization:list')
            ->setDescription('List all available organizations.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrganizationInterface[] $organizations */
        $organizations = $this->getContainer()->get('swp.repository.organization')->findAll();
        if (0 === count($organizations)) {
            $output->writeln('<error>There are no organizations defined.</error>');

            return;
        }

        $output->writeln('<info>List of all available organizations:</info>');
        $table = new Table($output);
        $table->setHeaders(['Id', 'Code', 'Name', 'Is active?', 'Created at']);
        foreach ($organizations as $organization) {
            $table->addRow([
                $organization->getId(),
                $organization->getCode(),
                $organization->getName(),
                $organization->isEnabled() ? 'yes' : 'no',
                $organization->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);
        }

        $table->render();
    }
}
