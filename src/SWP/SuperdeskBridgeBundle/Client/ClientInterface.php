<?php

/**
 * @copyright 2015 Sourcefabric z.ú.
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\SuperdeskBridgeBundle\Client;

/**
 * Interface for clients.
 */
interface ClientInterface
{
    /**
     * Make a call to the public api.
     *
     * @param string     $endpoint        Url path of the public api
     * @param array|null $queryParameters List of query parameters
     * @param array|null $options         List of options to send to the http client
     *
     * @return string Returns response body
     */
    public function makeApiCall($endpoint, $queryParameters = null, $options = null);
}
