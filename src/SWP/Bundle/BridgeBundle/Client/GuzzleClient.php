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

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Exception\TransferException as GuzzleTransferException;
use Superdesk\ContentApiSdk\Client\ClientInterface;
use Superdesk\ContentApiSdk\Exception\ClientException;

/**
 * Request service that implements all method regarding basic request/response
 * handling.
 */
class GuzzleClient extends BaseClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function makeCall(
        $url,
        array $headers = [],
        array $options = [],
        $method = 'GET',
        $content = null
    ) {
        $options['headers'] = $headers;

        if (in_array($method, ['POST'])) {
            $options['body'] = $content;
        }

        try {
            $response = $this->request($method, $url, $options);
        } catch (GuzzleClientException $e) {
            // This is for 400 errors
            $response = $e->getResponse();
        } catch (GuzzleServerException $e) {
            // This is for 500 errors
            $response = $e->getResponse();
        } catch (GuzzleTransferException $e) {
            // Any other errors should trigger an exception
            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        $responseArray = [
            'headers' => $response->getHeaders(),
            'status' => $response->getStatusCode(),
            'body' => (string) $response->getBody(),
        ];

        return $responseArray;
    }
}
