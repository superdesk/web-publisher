<?php

namespace SWP\UpdaterBundle\Version;

/**
 * Application version class.
 */
final class Version implements VersionInterface
{
    /**
     * Version string.
     *
     * @var string
     */
    private $version = '0.1.0';

    /**
     * Gets the value of version.
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the value of version.
     *
     * @param mixed $version the version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}
