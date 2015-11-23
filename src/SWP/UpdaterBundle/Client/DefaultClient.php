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
 * Default Client class.
 * Allows to fetch data from the remote update server.
 */
class DefaultClient implements ClientInterface
{
    /**
     * Default request options.
     *
     * @var array
     */
    private $options = array(
        'http' => array(
            'header' => array(
                "Accept: application/json\r\n",
            ),
        ),
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
        $context = stream_context_create($this->processOptions($options));
        $response = @file_get_contents(
            $this->buildUrl($endpoint, $this->processParameters($arguments)),
            false,
            $context
        );

        if ($response === false) {
            throw new ClientException('Invalid response.');
        }

        if ($fullResponse) {
            return $this->decodeResponse($response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function saveFile($fromUrl, $filePath)
    {
        file_put_contents($filePath, fopen($fromUrl, 'r'));
    }

    private function getBaseUrl()
    {
        return rtrim($this->baseUri, '/');
    }

    private function buildUrl($url, $params)
    {
        $url = sprintf(
            '%s/%s?%s',
            $this->getBaseUrl(),
            ltrim($url, '/'),
            ((!is_null($params)) ? http_build_query($params) : '')
        );

        return $url;
    }

    private function processParameters($params)
    {
        if (!is_array($params)) {
            return $params;
        }

        return $params;
    }

    private function processOptions($options)
    {
        if (is_array($options)) {
            $options = array_merge($this->options, $options);
        } else {
            $options = $this->options;
        }

        return $options;
    }

    private function decodeResponse($response)
    {
        return array(
            'headers' => array(),
            'status' => '200',
            'reason' => 'OK',
            'version' => '',
            'body' => $response,
        );
    }
}
