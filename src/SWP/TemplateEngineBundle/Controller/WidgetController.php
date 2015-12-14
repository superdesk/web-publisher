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

namespace SWP\TemplateEngineBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\TemplateEngineBundle\Model\Widget;
use SWP\TemplateEngineBundle\Form\Type\WidgetType;

class WidgetController extends FOSRestController
{
    /**
     * Lists all registered widgets
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered widgets",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/widgtes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_widgets")
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $paginator = $this->get('knp_paginator');
        $widgets = $paginator->paginate($entityManager->getRepository('SWP\TemplateEngineBundle\Model\Widget')->getAll());
    
        if (count($widgets) == 0) {
            throw new NotFoundHttpException('Widgets were not found.');
        }

        return $this->container->get('swp_pagination_rep')
            ->createRepresentation($widgets, $request);
    }

    /**
     * Get single widget
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
     * @Route("/api/{version}/templates/widgets/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_widget")
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function getAction(Request $request, $id)
    {
        if (!$id || !is_numeric($id)) {
            throw new UnprocessableEntityHttpException('You need to provide widget Id (integer).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $widget = $entityManager->getRepository('SWP\TemplateEngineBundle\Model\Widget')->getById($id)->getOneOrNullResult();

        if (!$widget) {
            throw new NotFoundHttpException('Widget with this id was not found.');
        }

        return $widget;
    }

    /**
     * Create new widget
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new widget",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     input="SWP\TemplateEngineBundle\Form\Type\WidgetType"
     * )
     * @Route("/api/{version}/templates/widgets", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_widget")
     * @Method("POST")
     * @Rest\View(statusCode=200)
     */
    public function createAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $widget = new Widget();
        $form = $this->createForm(new WidgetType(), $widget);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->persist($widget);
            $entityManager->flush();

            return $widget;
        }

        return $form;
    }
}
