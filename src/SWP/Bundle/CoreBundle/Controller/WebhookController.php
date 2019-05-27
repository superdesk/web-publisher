<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\WebhookBundle\Controller\AbstractAPIController;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;

class WebhookController extends AbstractAPIController
{
    /**
     * List all Webhook entities for current tenant.
     *
     * @Operation(
     *     tags={""},
     *     summary="List all webhooks",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_webhook")
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        return parent::listWebhooks($this->container->get('swp.repository.webhook'), $request);
    }

    /**
     * Get single Webhook.
     *
     * @Operation(
     *     tags={""},
     *     summary="Get single webhook",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/webhooks/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_webhook")
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @param WebhookInterface $webhook
     *
     * @return SingleResourceResponse
     */
    public function getAction(WebhookInterface $webhook)
    {
        return parent::getSingleWebhook($webhook);
    }

    /**
     * Create new Webhook.
     *
     * @Operation(
     *     tags={""},
     *     summary="Create new webhook",
     *     @SWG\Parameter(
     *         name="url",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="events",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="array of strings")
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned on validation error."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_webhook")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function createAction(Request $request)
    {
        $ruleRepository = $this->get('swp.repository.webhook');
        $ruleFactory = $this->get('swp.factory.webhook');
        $formFactory = $this->get('form.factory');

        return parent::createWebhook($ruleRepository, $ruleFactory, $request, $formFactory);
    }

    /**
     * Delete single webhook.
     *
     * @Operation(
     *     tags={""},
     *     summary="Delete single webhook",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Returned when method not allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_webhook", requirements={"id"="\d+"})
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @return SingleResourceResponse
     */
    public function deleteAction(WebhookInterface $webhook)
    {
        $webhookRepository = $this->get('swp.repository.webhook');

        return parent::deleteWebhook($webhookRepository, $webhook);
    }

    /**
     * Updates single webhook.
     *
     * @Operation(
     *     tags={""},
     *     summary="Update single webhook",
     *     @SWG\Parameter(
     *         name="url",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="events",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="array of strings")
     *     ),
     *     @SWG\Parameter(
     *         name="enabled",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned on validation error."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     *
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_webhook", requirements={"id"="\d+"})
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @param Request          $request
     * @param WebhookInterface $webhook
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request, WebhookInterface $webhook)
    {
        $objectManager = $this->get('swp.object_manager.webhook');
        $formFactory = $this->get('form.factory');

        return parent::updateWebhook($objectManager, $request, $webhook, $formFactory);
    }
}
