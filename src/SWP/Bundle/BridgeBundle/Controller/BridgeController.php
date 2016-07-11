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
namespace SWP\Bundle\BridgeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Superdesk\ContentApiSdk\Api\Authentication\OAuthPasswordAuthentication;
use Superdesk\ContentApiSdk\Api\Request\RequestParameters;
use Superdesk\ContentApiSdk\Client\ApiClientInterface;
use Superdesk\ContentApiSdk\ContentApiSdk;
use Superdesk\ContentApiSdk\Exception\ContentApiException;
use SWP\Bundle\BridgeBundle\Client\GuzzleApiClient;
use SWP\Bundle\BridgeBundle\Client\GuzzleClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/bridge")
 */
class BridgeController extends Controller
{
    /**
     * @Route("/{endpoint}/")
     * @Route("/{endpoint}/{objectId}/")
     * @Method("GET")
     *
     * Index action for bridge controller
     *
     * @param Request     $request
     * @param string      $endpoint Endpoint of the api
     * @param string|null $objectId Identifier of object to retrieve
     *
     * @throws ContentApiException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $endpoint, $objectId = null)
    {
        $data = [];
        $apiClient = $this->getClient();
        $sdk = $this->getSDK($apiClient);

        $parameters = $request->query->all();
        $requestParams = new RequestParameters();
        $requestParams->setQueryParameterArray($parameters);
        $endpointPath = sprintf('/%s', $endpoint);

        if ($this->isValidEndpoint($endpointPath)) {
            throw new ContentApiException(sprintf('Endpoint %s not supported.', $endpoint));
        }

        switch ($endpointPath) {
            case $sdk::SUPERDESK_ENDPOINT_ITEMS:

                if (!is_null($objectId)) {
                    $data = $sdk->getItem($objectId);
                } else {
                    $data = $sdk->getItems($requestParams);
                }
                break;

            case $sdk::SUPERDESK_ENDPOINT_PACKAGES:

                // TODO: Change this in the future to match the superdesk public api parameter name
                $resolve = (isset($parameters['resolveItems']) && $parameters['resolveItems']) ? true : false;

                if (!is_null($objectId)) {
                    $data = $sdk->getPackage($objectId, $resolve);
                } else {
                    $data = $sdk->getPackages($requestParams, $resolve);
                }
                break;
        }

        return $this->render('SWPBridgeBundle:Default:data_dump.html.twig', ['data' => $data]);
    }

    /**
     * Get an instance of the sdk.
     *
     * @param ApiClientInterface $apiClient Api HTTP Client
     *
     * @return ContentApiSdk
     */
    private function getSDK(ApiClientInterface $apiClient)
    {
        return new ContentApiSdk(
            $apiClient,
            $this->container->getParameter('swp_bridge.api.host'),
            $this->container->getParameter('swp_bridge.api.port'),
            $this->container->getParameter('swp_bridge.api.protocol')
        );
    }

    /**
     * Get an instance of the HTTP client. The returned class should implement
     * the \Superdesk\ContentApiSdk\Client\ApiClientInterface interface.
     *
     * @return GuzzleApiClient
     */
    private function getClient()
    {
        $authentication = new OAuthPasswordAuthentication(new GuzzleClient());
        $authentication
            ->setClientId($this->container->getParameter('swp_bridge.auth.client_id'))
            ->setUsername($this->container->getParameter('swp_bridge.auth.username'))
            ->setPassword($this->container->getParameter('swp_bridge.auth.password'));

        $apiClient = new GuzzleApiClient(new GuzzleClient(), $authentication);
        $apiClient->setOptions($this->container->getParameter('swp_bridge.options'));

        return $apiClient;
    }

    /**
     * Check if the supplied endpoint is supported by the SDK.
     *
     * @param string $endpoint Endpoint url (/ will be automatically prepended)
     *
     * @return bool
     */
    private function isValidEndpoint($endpoint)
    {
        return !in_array(sprintf('/%s', ltrim($endpoint, '/')), ContentApiSdk::getAvailableEndpoints());
    }
}
