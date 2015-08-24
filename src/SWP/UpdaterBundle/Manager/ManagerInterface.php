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

namespace SWP\UpdaterBundle\Manager;

/**
 * Update manager interface.
 */
interface ManagerInterface
{
    /**
     * Gets all available updates.
     *
     * @param $channel Updates channel
     *
     * @return array Available updates
     */
    public function getAvailableUpdates($channel);

    /**
     * Gets the latest available update.
     *
     * @return string latest update
     */
    public function getLatestUpdate();

    /**
     * Gets the current app version.
     *
     * @return string current app version
     */
    public function getCurrentVersion();
}
