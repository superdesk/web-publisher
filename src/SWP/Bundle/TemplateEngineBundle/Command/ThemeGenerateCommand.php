<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Command;

use SWP\Component\MultiTenancy\Model\Organization;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class ThemeGenerateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('theme:generate')
            ->setDescription('Creates basic theme structure with routes and empty templates.')
            ->addArgument(
                'organizationName',
                InputArgument::REQUIRED,
                'Organization name',
                null
            )
            ->addArgument(
                'siteName',
                InputArgument::REQUIRED,
                'Site name',
                null
            )
            ->setHelp(
                "The <info>%command.name%</info> command creates a skeleton theme in your application themes folder (app/themes)"
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $organization = $this->getOrganization($input);
        $site = $this->getSite($input, $organization);
    }

    protected function getOrganization(InputInterface $input)
    {
        $organizationName = $input->getArgument('organizationName');
        $this->getContainer()->get
    }

    protected function getSite(InputInterface $input, Organization $organization)
    {
        $siteName = $input->getArgument('siteName');
    }
}
