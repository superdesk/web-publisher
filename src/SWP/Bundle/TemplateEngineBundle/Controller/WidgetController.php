<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\TemplateEngineBundle\Form\Type\WidgetType;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
     */
    public function listAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $all = $this->get('swp_template_engine_revision')->getCurrentRevisions('SWP\Bundle\TemplateEngineBundle\Model\WidgetModel');
        $widgets = $paginator->paginate($all);

        if (count($widgets) == 0) {
            throw new NotFoundHttpException('Widgets were not found.');
        }

        return $this->handleView(View::create($this->container->get('swp_pagination_rep')->createRepresentation($widgets, $request), 200));
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
    public function getAction(Request $request, $id)
    {
        $widget = $this->getWidget($id);

        // return clean object for LINK requests
        if ($request->attributes->get('_link_request', false) === true) {
            return $widget;
        }

        return $this->handleView(View::create($widget, 200));
    }

    /**
     * Create new widget.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new widget",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\WidgetType"
     * )
     * @Route("/api/{version}/templates/widgets", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_widget")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $widget = new WidgetModel();
        $form = $this->createForm(new WidgetType(), $widget);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->persist($widget);
            $entityManager->flush();

            return $this->handleView(View::create($widget, 201));
        }

        return $this->handleView(View::create($form, 400));
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
    public function deleteAction(Request $request, $id)
    {
        $widget = $this->getWidget($id);

        $entityManager = $this->get('doctrine.orm.entity_manager');
        foreach ($widget->getContainers() as $containerWidget) {
            $entityManager->remove($containerWidget);
        }

        $entityManager->remove($widget);
        $entityManager->flush();

        return $this->handleView(View::create(null, 204));
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
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\WidgetType"
     * )
     * @Route("/api/{version}/templates/widgets/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_widget")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $widget = $this->getWidget($id);

        $revisionService = $this->get('swp_template_engine_revision');
        $unpublished = $revisionService->getOrCreateUnpublishedRevision($widget);

        // Set the name of unpublished version to be that of published version
        $unpublishedName = $unpublished->getName();
        $publishedName = $widget->getName();
        if ($revisionService->isNameUnchanged($widget, $unpublished)) {
            $unpublished->setName($publishedName);
        }

        $form = $this->createForm(new WidgetType(), $unpublished, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($publishedName === $unpublished->getName()) {
                $unpublished->setName($unpublishedName);
            }
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->flush($unpublished);
            $entityManager->refresh($unpublished);

            return $this->handleView(View::create($unpublished, 201));
        }

        return $this->handleView(View::create($form, 200));
    }

    /**
     * Publish changes to widget.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Publish changes to widget - returns published widget",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Widget not found",
     *         409="Widget is not published",
     *         410="No unpublished version"
     *     }
     * )
     * @Route("/api/{version}/templates/widgets/publish/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_publish_widget")
     * @Method("PUT")
     */
    public function publishAction($id)
    {
        $widget = $this->getWidget($id);
        $successor = $this->get('swp_template_engine_revision')->publishUnpublishedRevision($widget);

        return $this->handleView(View::create($successor, 201));
    }

    /**
     * @param $id
     *
     * @return WidgetModel
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getWidget($id)
    {
        if (!$id) {
            throw new UnprocessableEntityHttpException('You need to provide container Id (integer).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $widget = $entityManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\WidgetModel')->getById($id)->getOneOrNullResult();

        if (!$widget) {
            throw new NotFoundHttpException('Widget with this id was not found.');
        }

        return $widget;
    }
}
