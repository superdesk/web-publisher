<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver;

use SWP\Bundle\ContentBundle\DependencyInjection\Driver\Exception\InvalidDriverException;

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
        ];

        if (!isset(self::$supportedDrivers[$type])) {
            throw new InvalidDriverException($type);
        }

        $className = self::$supportedDrivers[$type];

        return new $className();
    }
}
