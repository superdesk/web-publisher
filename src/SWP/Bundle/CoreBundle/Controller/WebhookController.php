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

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\WebhookBundle\Controller\AbstractAPIController;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Component\HttpFoundation\Request;

class WebhookController extends AbstractAPIController
{
    /**
     * @Operation(
     *     tags={"webhook"},
     *     summary="List all Webhook entities for current tenant.",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [updatedAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Webhook::class, groups={"api"}))
     *         )
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
        return $this->listWebhooks($this->container->get('swp.repository.webhook'), $request);
    }

    /**
     * @Operation(
     *     tags={"webhook"},
     *     summary="Get single webhook",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Webhook::class, groups={"api"})
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
     */
    public function getAction(WebhookInterface $webhook): SingleResourceResponseInterface
    {
        return $this->getSingleWebhook($webhook);
    }

    /**
     * Create new Webhook.
     *
     * @Operation(
     *     tags={"webhook"},
     *     summary="Create new webhook",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=\SWP\Bundle\WebhookBundle\Form\Type\WebhookType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Webhook::class, groups={"api"})
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
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        $ruleRepository = $this->get('swp.repository.webhook');
        $ruleFactory = $this->get('swp.factory.webhook');
        $formFactory = $this->get('form.factory');

        return $this->createWebhook($ruleRepository, $ruleFactory, $request, $formFactory);
    }

    /**
     * Delete single webhook.
     *
     * @Operation(
     *     tags={"webhook"},
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
     */
    public function deleteAction(WebhookInterface $webhook): SingleResourceResponseInterface
    {
        $webhookRepository = $this->get('swp.repository.webhook');

        return $this->deleteWebhook($webhookRepository, $webhook);
    }

    /**
     * Updates single webhook.
     *
     * @Operation(
     *     tags={"webhook"},
     *     summary="Update single webhook",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=\SWP\Bundle\WebhookBundle\Form\Type\WebhookType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Webhook::class, groups={"api"})
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
     */
    public function updateAction(Request $request, WebhookInterface $webhook): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.webhook');
        $formFactory = $this->get('form.factory');

        return $this->updateWebhook($objectManager, $request, $webhook, $formFactory);
    }
}
