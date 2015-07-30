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
     * Makes an API call to an external server
     * to get the data from.
     *
     * @param string $endpoint API endpoint to call
     * @param array  $options  Array of parameters
     *
     * @return array|string Response from the server
     */
    public function call($endpoint = '/', array $options = array());

    /**
     * Checks whethere the connection to
     * a remote update server is alive.
     *
     * @param string $endpoint API endpoint
     *
     * @throws ConnectException When connection is dead
     */
    public function isAlive($endpoint = '/');
}
