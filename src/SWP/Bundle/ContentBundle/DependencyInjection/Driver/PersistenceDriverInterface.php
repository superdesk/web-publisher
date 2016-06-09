<?php

namespace SWP\Bundle\ContentBundle\DependencyInjection\Driver;

use Symfony\Component\DependencyInjection\ContainerBuilder;

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
}
