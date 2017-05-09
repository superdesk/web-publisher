<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Version;

/**
 * Application version class.
 */
final class Version
{
    /**
     * Version string.
     *
     * @var string
     */
    protected $version = '0.6.0';

    /**
     * Code name string.
     *
     * @var string
     */
    protected $codeName = '';

    /**
     * Release date string.
     *
     * @var string
     */
    protected $releaseDate = '2017-05-09';

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the Code name string.
     *
     * @return string
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Sets the Code name string.
     *
     * @param string $codeName the code name
     *
     * @return self
     */
    public function setCodeName($codeName)
    {
        $this->codeName = $codeName;

        return $this;
    }

    /**
     * Gets the Release date string.
     *
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Sets the Release date string.
     *
     * @param string $releaseDate the release date
     *
     * @return self
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }
}
