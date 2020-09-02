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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\WebhookBundle\Controller\AbstractAPIController;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractAPIController
{
    /**
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_webhook")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        return $this->listWebhooks($this->container->get('swp.repository.webhook'), $request);
    }

    /**
     * @Route("/api/{version}/webhooks/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_webhook")
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     */
    public function getAction(WebhookInterface $webhook): SingleResourceResponseInterface
    {
        return $this->getSingleWebhook($webhook);
    }

    /**
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
