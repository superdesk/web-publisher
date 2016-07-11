<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\BridgeBundle\Client;

use Superdesk\ContentApiSdk\Api\Request\RequestInterface;
use Superdesk\ContentApiSdk\Client\DefaultApiClient;

/**
 * Request service that implements all method regarding basic request/response
 * handling.
 */
class GuzzleApiClient extends DefaultApiClient
{
    /**
     * Options which come from Bundle configuration.
     *
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    protected function sendRequest(RequestInterface $request)
    {
        $request->setOptions(
            $this->addDefaultOptions(
                $request->getOptions()
            )
        );

        return parent::sendRequest($request);
    }

    /**
     * Merges property options with request options.
     *
     * @param array $options Request options
     *
     * @return array
     */
    public function addDefaultOptions($options)
    {
        return array_merge($options, $this->options);
    }

    /**
     * Sets default options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
