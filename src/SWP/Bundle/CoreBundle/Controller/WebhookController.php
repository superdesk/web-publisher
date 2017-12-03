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

use Doctrine\ORM\OptimisticLockException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\WebhookBundle\Form\Type\WebhookType;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebhookController extends FOSRestController
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
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_webhook")
     *
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     *
     * @throws NotFoundHttpException
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        $rules = $this->get('swp.repository.webhook')
            ->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        if (0 === $rules->count()) {
            throw new NotFoundHttpException('No webhooks were found.');
        }

        return new ResourcesListResponse($rules);
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
     * @Route("/api/{version}/webhooks/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_get_webhook")
     *
     * @Method("GET")
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @Cache(expires="10 minutes", public=true)
     *
     * @return SingleResourceResponse
     */
    public function getAction(WebhookInterface $webhook)
    {
        return new SingleResourceResponse($webhook);
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
     * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_create_webhook")
     *
     * @Method("POST")
     *
     * @return SingleResourceResponse
     */
    public function createAction(Request $request)
    {
        $ruleRepository = $this->get('swp.repository.webhook');

        $webhook = $this->get('swp.factory.webhook')->create();
        $form = $this->createForm(WebhookType::class, $webhook);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ruleRepository->add($webhook);

            return new SingleResourceResponse($webhook, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
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
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_webhook", requirements={"id"="\d+"})
     *
     * @Method("DELETE")
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @return SingleResourceResponse
     */
    public function deleteAction(WebhookInterface $webhook)
    {
        $webhookRepository = $this->get('swp.repository.webhook');
        $webhookRepository->remove($webhook);

        return new SingleResourceResponse(null, new ResponseContext(204));
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
     * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_webhook", requirements={"id"="\d+"})
     *
     * @Method("PATCH")
     *
     * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
     *
     * @param Request          $request
     * @param WebhookInterface $webhook
     *
     * @throws OptimisticLockException
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request, WebhookInterface $webhook)
    {
        $objectManager = $this->get('swp.object_manager.webhook');

        $form = $this->createForm(WebhookType::class, $webhook, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($webhook);

            return new SingleResourceResponse($webhook);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
