<?php

namespace SWP\UpdaterBundle\Client;

use GuzzleHttp\Client as BaseClient;

class Client extends BaseClient implements ClientInterface
{
    const PACKAGES_ENDPOINT = '/packages.json';

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
    public function getUpdates()
    {
        $this->isAlive();
        $response = $this->get(self::PACKAGES_ENDPOINT);
        $body = (string) $response->getBody();

        return (array) json_decode($body);
    }

    /**
     * {@inheritdoc}
     */
    public function isAlive()
    {
        $this->get('/');
    }
}
