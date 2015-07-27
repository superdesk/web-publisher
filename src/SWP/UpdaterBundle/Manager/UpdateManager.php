<?php

namespace SWP\UpdaterBundle\Manager;

/**
 * Update manager.
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class UpdateManager extends AbstractManager
{
    /**
     * {@inheritdoc}
     */
    public function getAllUpdates()
    {
        return $this->updates;
    }

    /**
     * {@inheritdoc}
     */
    public function checkUpdates()
    {
        $updates = $this->client->getUpdates();

        return $this->checkForLatestVersion($updates);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestVersion()
    {
        return $this->latestVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }
}
