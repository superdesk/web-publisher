<?php

namespace SWP\UpdaterBundle\Manager;

/**
 * Update manager interface.
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
interface ManagerInterface
{
    /**
     * Gets all available updates.
     *
     * @return array Available updates
     */
    public function getAllUpdates();

    /**
     * Checks if new updates are available.
     *
     * @return bool true when there is a new version
     */
    public function checkUpdates();

    /**
     * Gets the latest available version.
     *
     * @return string latest version
     */
    public function getLatestVersion();

    /**
     * Gets the current app version.
     *
     * @return string current app version
     */
    public function getCurrentVersion();
}
