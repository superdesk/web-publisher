<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\StorageBundle\DependencyInjection\Driver;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;

interface PersistenceDriverInterface
{
    /**
     * Loads needed driver definitions into the container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config);

    /**
     * Gets Object Manager service identifier.
     *
     * @return string
     */
    public function getObjectManagerId();

    /**
     * Gets fully qualified meta data class name.
     *
     * @return string
     */
    public function getClassMetadataClassName();

    /**
     * Checks whether the driver is supported or not by given type.
     *
     * @param string $type
     */
    public function isSupported($type);

    /**
     * Gets the repository class name model for driver.
     *
     * @return Parameter
     */
    public function getDriverRepositoryParameter();
}
