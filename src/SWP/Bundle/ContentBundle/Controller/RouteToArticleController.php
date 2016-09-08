<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use SWP\Bundle\ContentBundle\Doctrine\ORM\RouteToArticle;
use SWP\Bundle\ContentBundle\Form\Type\RouteToArticleType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RouteToArticleController extends FOSRestController
{
    /**
     * List all RouteToArticle entities for current tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all RouteToArticleEntities for current tenant",
     *     statusCodes={
     *         200="Returned on success.",
     *     }
     * )
     * @Route("/api/{version}/mapping/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_mapping_list_route_to_article")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $paginator = $this->get('knp_paginator');
        $routeToArticles = $paginator->paginate($entityManager->getRepository('SWPContentBundle:RouteToArticle')->findAll());

        if (count($routeToArticles) == 0) {
            throw new NotFoundHttpException('RouteToArticles were not found.');
        }

        return $this->handleView(View::create($this->container->get('swp_pagination_rep')->createRepresentation($routeToArticles, $request), 200));
    }

    /**
     * Get single RouteToArticle Entity.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single RouteToArticle Entity",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Entity not found",
     *     }
     * )
     * @Route("/api/{version}/mapping/routes/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_mapping_get_route_to_article")
     * @Method("GET")
     * @ParamConverter("routeToArticle", class="SWPContentBundle:RouteToArticle")
     */
    public function getAction(RouteToArticle $routeToArticle)
    {
        return $this->handleView(View::create($routeToArticle, 200));
    }

    /**
     * Create new RouteToArticle Entity.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new RouteToArticle Entity",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form has errors"
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\RouteToArticleType"
     * )
     * @Route("/api/{version}/mapping/routes", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_mapping_create_route_to_article")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $routeToArticle = new RouteToArticle();
        $form = $this->createForm(RouteToArticleType::class, $routeToArticle);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->persist($routeToArticle);
            $entityManager->flush();

            return $this->handleView(View::create($routeToArticle, 201));
        }

        return $this->handleView(View::create($form, 400));
    }

    /**
     * Delete single RouteToArticle entity.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single RouteToArticle entity",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Entity not found",
     *     }
     * )
     * @Route("/api/{version}/mapping/routes/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_mapping_delete_route_to_article")
     * @Method("DELETE")
     * @ParamConverter("routeToArticle", class="SWPContentBundle:RouteToArticle")
     */
    public function deleteAction(RouteToArticle $routeToArticle)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $entityManager->remove($routeToArticle);
        $entityManager->flush();

        return $this->handleView(View::create(null, 204));
    }

    /**
     * Update single RouteToArticle entity.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single widget",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         404="Entity not found",
     *         405="Method Not Allowed"
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\RouteToArticleType"
     * )
     * @Route("/api/{version}/mapping/routes/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_mapping_update_route_to_article")
     * @Method("PATCH")
     * @ParamConverter("routeToArticle", class="SWPContentBundle:RouteToArticle")
     */
    public function updateAction(Request $request, RouteToArticle $routeToArticle)
    {
        $entityManager = $this->get('doctrine')->getManager();

        $form = $this->createForm(new RouteToArticleType(), $routeToArticle, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->flush($routeToArticle);
            $entityManager->refresh($routeToArticle);

            return $this->handleView(View::create($routeToArticle, 201));
        }

        return $this->handleView(View::create($form, 200));
    }
}
