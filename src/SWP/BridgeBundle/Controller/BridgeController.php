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

namespace SWP\BridgeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Superdesk\ContentApiSdk\ContentApiSdk;
use Superdesk\ContentApiSdk\Client\Client;
use Superdesk\ContentApiSdk\Exception\ContentApiException;

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
     * Indexaction for bridge controller
     *
     * @param Request     $request
     * @param string      $endpoint Endpoint of the api
     * @param string|null $objectId Identifier of object to retrieve
     *
     * @return Response
     */
    public function indexAction(Request $request, $endpoint, $objectId = null)
    {
        $data = array();
        $bridgeConfig = array(
            'base_uri' => $this->container->getParameter('swp_bridge.base_uri'),
            'options' => $this->container->getParameter('swp_bridge.options'),
        );

        $bridge = new ContentApiSdk(new Client($bridgeConfig));
        $parameters = $request->query->all();
        $endpointPath = sprintf('/%s', $endpoint);

        if (!in_array($endpointPath, $bridge->getAvailableEndpoints())) {
            throw new ContentApiException(sprintf('Endpoint %s not supported.', $endpoint));
        }

        if ($endpointPath === $bridge::SUPERDESK_ENDPOINT_ITEMS) {
            if (!is_null($objectId)) {
                $data = $bridge->getItem($objectId);
            } else {
                $data = $bridge->getItems($parameters);
            }
        } elseif ($endpointPath === $bridge::SUPERDESK_ENDPOINT_PACKAGES) {
            // TODO: Check if we can do this nicer
            $resolve = (isset($parameters['resolveItems']) && $parameters['resolveItems']) ? true : false;
            unset($parameters['resolveItems']);

            if (!is_null($objectId)) {
                $data = $bridge->getPackage($objectId, $resolve);
            } else {
                $data = $bridge->getPackages($parameters, $resolve);
            }
        }

        return $this->render('SWPBridgeBundle:Default:data_dump.html.twig', array('data' => $data));
    }
}
