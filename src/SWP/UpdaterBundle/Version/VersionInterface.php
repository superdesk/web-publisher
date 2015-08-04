<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
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
