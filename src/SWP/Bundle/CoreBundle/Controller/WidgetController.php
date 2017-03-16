<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Bundle\TemplatesSystemBundle\Form\Type\WidgetType;
use SWP\Bundle\CoreBundle\Model\WidgetModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WidgetController extends FOSRestController
{
    /**
     * Lists all registered widgets.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered widgets",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/widgets/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_widgets")
     * @Method("GET")
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.widget_model');

        $widgets = $repository->getPaginatedByCriteria(new Criteria(), [], new PaginationData($request));

        return new ResourcesListResponse($widgets);
    }

    /**
     * Get single widget.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single widget",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Widget not found",
     *         422="Widget id is not number"
     *     }
     * )
     * @Route("/api/{version}/templates/widgets/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_widget")
     * @Method("GET")
     */
    public function getAction($id)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $widget = $entityManager->getRepository('SWP\Bundle\CoreBundle\Model\WidgetModel')->getById($id)->getQuery()->getOneOrNullResult();

        if (!$widget) {
            throw new NotFoundHttpException('WidgetModel with this id was not found.');
        }

        return new SingleResourceResponse($widget);
    }

    /**
     * Create new widget.
     *
     * Note:
     *
     *     Widget Type can be widget ID or his class (string).
     *     Example: "SWP\\Bundle\\CoreBundle\\Widget\\ContentListWidget"
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new widget",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\TemplatesSystemBundle\Form\Type\WidgetType"
     * )
     * @Route("/api/{version}/templates/widgets/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_widget")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $widget = new WidgetModel();
        $form = $this->createForm(WidgetType::class, $widget);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->persist($widget);
            $entityManager->flush();

            return new SingleResourceResponse($widget, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Delete single widget.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single widget",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Widget not found",
     *         422="Widget id is not number"
     *     }
     * )
     * @Route("/api/{version}/templates/widgets/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_delete_widget")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $widget = $entityManager->getRepository('SWP\Bundle\CoreBundle\Model\WidgetModel')->getById($id)->getQuery()->getOneOrNullResult();

        if (!$widget) {
            throw new NotFoundHttpException('Widget with this id was not found.');
        }

        foreach ($widget->getContainers() as $containerWidget) {
            $entityManager->remove($containerWidget);
        }

        $entityManager->remove($widget);
        $entityManager->flush();

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * Update single widget.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single widget",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Widget not found",
     *         422="Widget id is not number",
     *         405="Method Not Allowed"
     *     },
     *     input="SWP\Bundle\TemplatesSystemBundle\Form\Type\WidgetType"
     * )
     * @Route("/api/{version}/templates/widgets/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_widget")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $widget = $entityManager->getRepository('SWP\Bundle\CoreBundle\Model\WidgetModel')->getById($id)->getQuery()->getOneOrNullResult();

        if (!$widget) {
            throw new NotFoundHttpException('Widget with this id was not found.');
        }

        $form = $this->createForm(WidgetType::class, $widget, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->flush($widget);
            $entityManager->refresh($widget);

            return new SingleResourceResponse($widget, new ResponseContext(201));
        }

        return new SingleResourceResponse($form);
    }
}
