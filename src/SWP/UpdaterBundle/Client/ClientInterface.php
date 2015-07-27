<?php

namespace SWP\UpdaterBundle\Client;

use GuzzleHttp\Exception\ConnectException;

/**
 * Client interface.
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
interface ClientInterface
{
    /**
     * Gets the list of available updates,
     * provided by update server API.
     *
     * @return array List of updates
     */
    public function getUpdates();

    /**
     * Checks whethere the connection to
     * a remote update server is alive.
     *
     * @throws ConnectException
     */
    public function isAlive();
}
