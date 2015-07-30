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

namespace SWP\SuperdeskBridgeBundle\Bridge;

use SWP\SuperdeskBridgeBundle\Client\Client;
use SWP\SuperdeskBridgeBundle\Data\Item;
use SWP\SuperdeskBridgeBundle\Data\Package;

/**
 * Superdesk Bridge class.
 */
class Bridge
{
    const SUPERDESK_ENDPOINT_ITEMS = '/items';
    const SUPERDESK_ENDPOINT_PACKAGES = '/packages';

    /**
     * Internal request service.
     *
     * @var SuperdeskBridgeRequestService
     */
    protected $client;

    /**
     * Configuration array.
     *
     * @var array
     */
    protected $config = array();

    /**
     * Construct method for class.
     *
     * @param Client $client
     * @param array  $config Configuration array
     */
    public function __construct(Client $client, $config)
    {
        $this->client = $client;
        $this->config = array_merge($this->config, (is_array($config)) ? $config : null);
        $this->client->setConfig($this->config);
    }

    /**
     * Get a single item via id.
     *
     * @param string $itemId Identifier for item
     *
     * @return mixed
     */
    public function getItem($itemId)
    {
        $item = null;
        $body = $this->client->makeApiCall(sprintf('%s/%s', self::SUPERDESK_ENDPOINT_ITEMS, $itemId));

        // TODO: Built in data-type check
        $item = new Item(json_decode($body));

        return $item;
    }

    /**
     * Get multiple items based on a filter.
     *
     * @param array $params Filter parameters
     *
     * @return mixed
     */
    public function getItems($params)
    {
        $body = $this->client->makeApiCall(self::SUPERDESK_ENDPOINT_ITEMS, $params);

        // TODO: Built in data-type check
        $bodyJSONObj = json_decode($body);

        foreach ($bodyJSONObj->_items as $key => $item) {
            $bodyJSONObj->_items[$key] = new Item($item);
        }

        return $bodyJSONObj->_items;
    }

    /**
     * Get package by identifier.
     *
     * @param string $packageId    Package identifier
     * @param bool   $resolveItems Inject full associations instead of references
     *                             by uri.
     *
     * @return stdClass
     */
    public function getPackage($packageId, $resolveItems = false)
    {
        $package = null;
        $body = $this->client->makeApiCall(sprintf('%s/%s', self::SUPERDESK_ENDPOINT_PACKAGES, $packageId));

        // TODO: Built in data-type check
        $package = new Package(json_decode($body));

        if ($resolveItems) {
            $associations = $this->getAssociationsFromPackage($package);
            $package = $this->injectAssociations($package, $associations);
        }

        return $package;
    }

    /**
     * Get multiple packages based on a filter.
     *
     * @param array $params       Filter parameters
     * @param bool  $resolveItems Inject full item data in reponse
     *
     * @return mixed
     */
    public function getPackages($params, $resolveItems = false)
    {
        $packages = null;
        $body = $this->client->makeApiCall(self::SUPERDESK_ENDPOINT_PACKAGES, $params);

        // TODO: Built in data-type check
        $bodyJSONObj = json_decode($body);

        foreach ($bodyJSONObj->_items as $key => $item) {
            $bodyJSONObj->_items[$key] = new Package($item);
        }
        $packages = $bodyJSONObj->_items;

        if ($resolveItems) {
            foreach ($packages as $id => $package) {
                $associations = $this->getAssociationsFromPackage($package);
                $packages[$id] = $this->injectAssociations($package, $associations);
            }
        }

        return $packages;
    }

    /**
     * Gets full objects for all associations for a package.
     *
     * @param stdClass $packages A package
     *
     * @return stdClass List of associations
     */
    private function getAssociationsFromPackage($package)
    {
        $associations = new \stdClass();

        if (isset($package->associations)) {
            foreach ($package->associations as $associatedName => $associatedItem) {
                $associatedId = $this->getIdFromUri($associatedItem->uri);

                // TODO: Check if we can make asynchronous calls here
                if ($associatedItem->type == 'composite') {
                    $associatedObj = $this->getPackage($associatedId, true);
                } else {
                    $associatedObj = $this->getItem($associatedId);
                }

                $associations->$associatedName = $associatedObj;
            }
        }

        return $associations;
    }

    /**
     * Tries to find a valid id in an uri, both item as package uris. The id
     * is returned urldecoded.
     *
     * @param string $uri Item or package uri
     *
     * @return string Urldecoded id
     */
    public function getIdFromUri($uri)
    {
        /*
         * Works for package and item uris
         *   http://publicapi:5050/packages/tag%3Ademodata.org%2C0012%3Aninjs_XYZ123
         *   http://publicapi:5050/items/tag%3Ademodata.org%2C0003%3Aninjs_XYZ123
         */

        $uriPath = parse_url($uri, PHP_URL_PATH);
        $objectId = str_replace($this->getAvailableEndpoints(), '', $uriPath);
        // Remove possible slashes and spaces, since we're working with urls
        $objectId = trim($objectId, '/ ');
        $objectId = urldecode($objectId);

        return $objectId;
    }

    /**
     * Overwrite the associations links in a packages with the actual association
     * data.
     *
     * @param stdClass $package      Package
     * @param stdClass $associations Multiple items or packages
     *
     * @return stdClass Package with data injected
     */
    private function injectAssociations($package, $associations)
    {
        if (count($package->associations) > 0 && count($associations) > 0) {
            $package->associations = $associations;
        }

        return $package;
    }

    /**
     * Returns a list of all supported endpoint for the Superdesk Publicapi.
     *
     * @return array
     */
    public static function getAvailableEndpoints()
    {
        return array(
            self::SUPERDESK_ENDPOINT_ITEMS,
            self::SUPERDESK_ENDPOINT_PACKAGES,
        );
    }
}
