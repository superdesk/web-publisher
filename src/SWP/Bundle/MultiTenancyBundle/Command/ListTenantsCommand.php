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

use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListTenantsCommand extends Command
{
    protected static $defaultName = 'swp:tenant:list';

    private $organizationRepository;

    private $tenantRepository;

    public function __construct(OrganizationRepositoryInterface $organizationRepository, TenantRepositoryInterface $tenantRepository)
    {
        parent::__construct();

        $this->organizationRepository = $organizationRepository;
        $this->tenantRepository = $tenantRepository;
    }

    protected function configure()
    {
        $this
            ->setName('swp:tenant:list')
            ->setDescription('List all available tenants.')
            ->setDefinition([
                new InputOption('organization', 'o', InputOption::VALUE_REQUIRED, 'Organization code (ex: 123456)', null),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* @var ThemeAwareTenantInterface[] $tenants */
        if (null !== $input->getOption('organization')) {
            $organization = $this->organizationRepository->findOneByCode($input->getOption('organization'));
            $tenants = $this->tenantRepository->findBy([
                'organization' => $organization,
            ]);
        } else {
            $tenants = $this->tenantRepository->findAll();
        }

        if (null === $tenants || 0 === count($tenants)) {
            $output->writeln('<error>There are no tenants defined.</error>');

            return 0;
        }

        $output->writeln('<info>List of all available tenants:</info>');
        $table = new Table($output);
        $table->setHeaders(['Id', 'Code', 'Name', 'Domain', 'Subdomain', 'Is active?', 'Theme Name', 'AMP Enabled', 'Created at', 'Organization']);
        foreach ($tenants as $tenant) {
            $table->addRow([
                $tenant->getId(),
                $tenant->getCode(),
                $tenant->getName(),
                $tenant->getDomainName(),
                $tenant->getSubdomain(),
                $tenant->isEnabled() ? 'yes' : 'no',
                $tenant->getThemeName(),
                $tenant->isAmpEnabled() ? 'yes' : 'no',
                $tenant->getCreatedAt()->format('Y-m-d H:i:s'),
                $tenant->getOrganization()->getName().' (code: '.$tenant->getOrganization()->getCode().')',
            ]);
        }

        $table->render();

        return 1;
    }
}
