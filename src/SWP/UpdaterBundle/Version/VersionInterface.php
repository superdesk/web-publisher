<?php

namespace SWP\UpdaterBundle\Version;

/**
 * Version interface.
 */
interface VersionInterface
{
    /**
     * Gets the value of version.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Sets the value of version.
     *
     * @param string $version the version
     */
    public function setVersion($version);
}
