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
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\Http\Message\ResponseInterface;

/**
 * Remote Client class.
 * Allows to fetch data from the remote update server.
 */
class GuzzleClient extends BaseClient implements ClientInterface
{
    /**
     * Default request options.
     *
     * @var array
     */
    private $options = array(
        'Accept' => 'application/json',
    );

    /**
     * Remote's server URI.
     *
     * @var string
     */
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
    public function call(
        $endpoint = '/',
        array $arguments = array(),
        array $options = array(),
        $fullResponse = false
    ) {
        try {
            $response = $this->get(
                $endpoint,
                $this->process($arguments, $options)
            );
        } catch (\Exception $e) {
            throw new ServiceUnavailableHttpException(
                null,
                'Could not resolve host: '.$this->baseUri,
                $e,
                $e->getCode()
            );
        }

        if ($fullResponse) {
            return $this->decode($response);
        }

        return (string) $response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function saveFile($fromUrl, $filePath)
    {
        $this->get($fromUrl, array(
            'save_to' => $filePath,
        ));
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

    private function decode(ResponseInterface $response)
    {
        return array(
            'headers' => $response->getHeaders(),
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'version' => $response->getProtocolVersion(),
            'body' => (string) $response->getBody(),
        );
    }
}
