<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancyBundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Component\MultiTenancy\Factory\TenantFactoryInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateTenantCommand extends Command
{
    protected static $defaultName = 'swp:tenant:create';

    protected $arguments = ['domain', 'subdomain', 'name', 'organization code'];

    private $swpDomain;

    private $tenantFactory;

    private $tenantObjectManager;

    private $tenantRepository;

    private $organizationRepository;

    public function __construct(
        string $swpDomain,
        TenantFactoryInterface $tenantFactory,
        ObjectManager $tenantObjectManager,
        TenantRepositoryInterface $tenantRepository,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        parent::__construct();

        $this->swpDomain = $swpDomain;
        $this->tenantFactory = $tenantFactory;
        $this->tenantObjectManager = $tenantObjectManager;
        $this->tenantRepository = $tenantRepository;
        $this->organizationRepository = $organizationRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('swp:tenant:create')
            ->setDescription('Creates a new tenant.')
            ->setDefinition([
                new InputArgument($this->arguments[3], InputArgument::OPTIONAL, 'Organization code'),
                new InputArgument($this->arguments[0], InputArgument::OPTIONAL, 'Domain name'),
                new InputArgument($this->arguments[2], InputArgument::OPTIONAL, 'Tenant name'),
                new InputArgument($this->arguments[1], InputArgument::OPTIONAL, 'Subdomain name', null),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the tenant as a disabled'),
                new InputOption('default', null, InputOption::VALUE_NONE, 'Creates the default tenant'),
            ])
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command creates a new tenant.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domain = $input->getArgument($this->arguments[0]);
        $subdomain = $input->getArgument($this->arguments[1]);
        $name = $input->getArgument($this->arguments[2]);
        $organizationCode = $input->getArgument($this->arguments[3]);
        $default = $input->getOption('default');
        $disabled = $input->getOption('disabled');

        if ($default) {
            $name = TenantInterface::DEFAULT_TENANT_NAME;
            $domain = $this->swpDomain;
            $organization = $this->organizationRepository->findOneByName(OrganizationInterface::DEFAULT_NAME);
            if (null === $organization) {
                throw new \InvalidArgumentException('Default organization doesn\'t exist!');
            }
        } else {
            $organization = $this->organizationRepository->findOneByCode($organizationCode);

            if (null === $organization) {
                throw new \InvalidArgumentException(sprintf('Organization with "%s" code doesn\'t exist!', $organizationCode));
            }
        }

        if (null !== $subdomain) {
            $tenant = $this->tenantRepository->findOneBySubdomainAndDomain($subdomain, $domain);
        } else {
            $tenant = $this->tenantRepository->findOneByDomain($domain);
        }

        if (null !== $tenant) {
            throw new \InvalidArgumentException(sprintf('Tenant with domain %s and subdomain "%s" already exists!', $domain, $subdomain));
        }

        $tenant = $this->createTenant($domain, $subdomain, $name, $disabled, $organization);
        if ($default) {
            $tenant->setCode(TenantInterface::DEFAULT_TENANT_CODE);
        }
        $this->tenantObjectManager->persist($tenant);
        $this->tenantObjectManager->flush();

        $output->writeln(
            sprintf(
                'Tenant <info>%s</info> (code: <info>%s</info>) has been created and <info>%s</info>!',
                $name,
                $tenant->getCode(),
                $tenant->isEnabled() ? 'enabled' : 'disabled'
            )
        );

        return 1;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->arguments as $value) {
            $this->askAndValidateInteract($input, $output, $value);
        }
    }

    protected function askAndValidateInteract(InputInterface $input, OutputInterface $output, $name): void
    {
        $default = $input->getOption('default');
        if (!$input->getArgument($name) && !$default) {
            $question = new Question(sprintf('<question>Please enter %s:</question>', $name));
            $question->setValidator(function ($argument) use ($name) {
                if (empty($argument) && $name !== $this->arguments[1]) {
                    throw new \RuntimeException(sprintf('The %s can not be empty', $name));
                }

                return $argument;
            });

            $question->setMaxAttempts(3);

            $argument = $this->getHelper('question')->ask($input, $output, $question);

            $input->setArgument($name, $argument);
        }
    }

    protected function createTenant(string $domain, ?string $subdomain, string $name, bool $disabled, OrganizationInterface $organization): TenantInterface
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantFactory->create();
        $tenant->setSubdomain($subdomain);
        $tenant->setDomainName($domain);
        $tenant->setName($name);
        $tenant->setEnabled(!$disabled);
        $tenant->setOrganization($organization);

        return $tenant;
    }
}
