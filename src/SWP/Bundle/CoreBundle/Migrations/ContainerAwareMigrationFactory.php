<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Decorates the Doctrine migrations factory to inject the service container
 * into migrations implementing {@see ContainerAwareInterface}. Replaces the
 * bundle's ContainerAwareMigrationFactory, which is removed under Symfony 7.
 */
final class ContainerAwareMigrationFactory implements MigrationFactory
{
    private MigrationFactory $migrationFactory;

    private ContainerInterface $container;

    public function __construct(MigrationFactory $migrationFactory, ContainerInterface $container)
    {
        $this->migrationFactory = $migrationFactory;
        $this->container = $container;
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = $this->migrationFactory->createVersion($migrationClassName);

        if ($migration instanceof ContainerAwareInterface) {
            $migration->setContainer($this->container);
        }

        return $migration;
    }
}
