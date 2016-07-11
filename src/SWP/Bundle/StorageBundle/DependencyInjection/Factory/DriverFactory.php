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
namespace SWP\Bundle\StorageBundle\DependencyInjection\Factory;

use SWP\Bundle\StorageBundle\DependencyInjection\Driver\ORMDriver;
use SWP\Bundle\StorageBundle\DependencyInjection\Driver\PHPCRDriver;
use SWP\Component\Storage\DependencyInjection\Factory\DriverFactoryInterface;
use SWP\Component\Storage\Exception\InvalidDriverException;

class DriverFactory implements DriverFactoryInterface
{
    /**
     * @var array
     */
    private static $supportedDrivers = [];

    /**
     * {@inheritdoc}
     */
    public static function createDriver($type)
    {
        self::$supportedDrivers = [
            PHPCRDriver::$type => PHPCRDriver::class,
            ORMDriver::$type => ORMDriver::class,
        ];

        if (!isset(self::$supportedDrivers[$type])) {
            throw new InvalidDriverException($type);
        }

        $className = self::$supportedDrivers[$type];

        return new $className();
    }
}
