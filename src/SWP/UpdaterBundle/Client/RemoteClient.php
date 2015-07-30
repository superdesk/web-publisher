<?php

namespace SWP\UpdaterBundle\Client;

use GuzzleHttp\Client as BaseClient;

/**
 * Remote Client class.
 * Allows to fetch data from the remote update server.
 */
class RemoteClient extends BaseClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $config = array())
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function call($endpoint = '/', array $options = array())
    {
        $this->isAlive();
        $response = $this->get($endpoint, $options);

        return $this->decode($response);
    }

    /**
     * {@inheritdoc}
     */
    public function isAlive($endpoint = '/')
    {
        $this->get($endpoint);
    }

    private function decode($response)
    {
        $body = (string) $response->getBody();

        return (array) json_decode($body, true);
    }
}
