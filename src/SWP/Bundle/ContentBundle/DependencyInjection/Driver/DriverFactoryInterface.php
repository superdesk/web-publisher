<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver;

interface DriverFactoryInterface
{
    /**
     * Creates a new instance of driver.
     *
     * @param string $type
     *
     * @return PersistenceDriverInterface
     */
    public static function createDriver($type);
}
