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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *     resource=true,
     *     description="List all webhooks",
     *     statusCodes={
     *         200="Returned on success.",
     *         405="Method Not Allowed."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v1"}, methods={"GET"}, name="swp_api_core_list_webhook")
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
     * @ApiDoc(
     *     resource=true,
     *     description="Get single webhook",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Rule not found.",
     *         405="Method Not Allowed."
     *     }
     * )
     * @Route("/api/{version}/webhooks/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, methods={"GET"}, name="swp_api_core_get_webhook")
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
     * @ApiDoc(
     *     resource=true,
     *     description="Create new webhook",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\WebhookBundle\Form\Type\WebhookType"
     * )
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v1"}, methods={"POST"}, name="swp_api_core_create_webhook")
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
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single webhook",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Returned when rule not found.",
     *         405="Returned when method not allowed."
     *     }
     * )
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v1"}, methods={"DELETE"}, name="swp_api_core_delete_webhook", requirements={"id"="\d+"})
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
     * @ApiDoc(
     *     resource=true,
     *     description="Update single webhook",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         404="Rule not found.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\WebhookBundle\Form\Type\WebhookType"
     * )
     *
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v1"}, methods={"PATCH"}, name="swp_api_core_update_webhook", requirements={"id"="\d+"})
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
