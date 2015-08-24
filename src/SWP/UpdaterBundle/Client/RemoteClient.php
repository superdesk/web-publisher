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

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Remote Client class.
 * Allows to fetch data from the remote update server.
 */
class RemoteClient extends BaseClient implements ClientInterface
{
    private $options = array();
    private $baseUri;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config = array(), array $options = array())
    {
        parent::__construct($config);
        $this->options = $options;
        $this->baseUri = $config['base_uri'];
    }

    /**
     * {@inheritdoc}
     */
    public function call($endpoint = '/', array $arguments = array(), array $options = array())
    {
        try {
            $response = $this->get(
                $endpoint,
                $this->process($arguments, $options)
            );
        } catch (ConnectException $e) {
            throw new ServiceUnavailableHttpException(
                null,
                'Could not resolve host: '.$this->baseUri,
                $e,
                $e->getCode()
            );
        }

        return $this->decode($response);
    }

    private function process($arguments, $options)
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        // add query parameters
        $this->options['query'] = $arguments;

        return $this->options;
    }

    private function decode($response)
    {
        $body = (string) $response->getBody();

        return (array) json_decode($body, true);
    }
}
