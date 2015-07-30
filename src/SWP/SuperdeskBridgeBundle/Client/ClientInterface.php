<?php

/**
 * This file is part of the PHP SDK library for the Superdesk Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
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
