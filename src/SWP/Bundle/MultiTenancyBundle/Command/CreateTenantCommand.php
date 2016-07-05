<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancyBundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\MultiTenancy\Model\Tenant;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateTenantCommand.
 */
class CreateTenantCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    protected $arguments = ['subdomain', 'name'];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swp:tenant:create')
            ->setDescription('Creates a new tenant.')
            ->setDefinition([
                new InputArgument($this->arguments[0], InputArgument::OPTIONAL, 'Subdomain name'),
                new InputArgument($this->arguments[1], InputArgument::OPTIONAL, 'Tenant name'),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the tenant as a disabled'),
                new InputOption('default', null, InputOption::VALUE_NONE, 'Creates the default tenant'),
            ])
            ->setHelp(
                <<<'EOT'
                The <info>%command.name%</info> command creates a new tenant.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subdomain = $input->getArgument($this->arguments[0]);
        $name = $input->getArgument($this->arguments[1]);
        $default = $input->getOption('default');
        $disabled = $input->getOption('disabled');

        if ($default) {
            $subdomain = 'default';
            $name = 'Default tenant';
        }

        $tenant = $this->getTenantRepository()->findBySubdomain($subdomain);
        if (null !== $tenant) {
            throw new \InvalidArgumentException(sprintf('Tenant with subdomain "%s" already exists!', $subdomain));
        }

        $tenant = $this->createTenant($subdomain, $name, $disabled);

        $this->getEntityManager()->persist($tenant);
        $this->getEntityManager()->flush();

        $output->writeln(
            sprintf(
                'Tenant <info>%s</info> has been created and <info>%s</info>!',
                $name,
                $tenant->isEnabled() ? 'enabled' : 'disabled'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->arguments as $value) {
            $this->askAndValidateInteract($input, $output, $value);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $name
     */
    protected function askAndValidateInteract(InputInterface $input, OutputInterface $output, $name)
    {
        $default = $input->getOption('default');
        if (!$input->getArgument($name) && !$default) {
            $argument = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Please enter '.$name.':</question>',
                function ($argument) use ($name) {
                    if (empty($argument)) {
                        throw new \RuntimeException('The '.$name.' can not be empty');
                    }

                    return $argument;
                }
            );

            $input->setArgument($name, $argument);
        }
    }

    /**
     * Creates a new tenant.
     *
     * @param $subdomain
     * @param $name
     * @param $disabled
     *
     * @return TenantInterface
     */
    protected function createTenant($subdomain, $name, $disabled)
    {
        $tenantFactory = $this->getContainer()->get('swp_multi_tenancy.factory.tenant');
        $tenant = $tenantFactory->create();
        $tenant->setSubdomain($subdomain);
        $tenant->setName($name);
        $tenant->setEnabled(!$disabled);

        return $tenant;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return TenantRepositoryInterface
     */
    protected function getTenantRepository()
    {
        return $this->getContainer()->get('swp_multi_tenancy.tenant_repository');
    }
}
