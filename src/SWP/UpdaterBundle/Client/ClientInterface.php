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
namespace SWP\UpdaterBundle\Client;

/**
 * Client interface.
 */
interface ClientInterface
{
    /**
     * Makes an API call to an external server
     * to get the data from.
     *
     * @param string $endpoint  API endpoint to call
     * @param array  $arguments An array of arguments
     * @param arrat  $options   An array of extra parameters
     *
     * @return array Response from the server
     */
    public function call($endpoint = '/', array $arguments = array(), array $options = array());
}
