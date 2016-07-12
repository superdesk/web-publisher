<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteController extends FOSRestController
{
    /**
     * Lists current tenant routes.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists current tenant routes",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_routes")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $baseroute = $this->get('swp.provider.route')->getBaseRoute();
        $routes = [];

        if (null !== $baseroute) {
            $routes = $this->get('knp_paginator')->paginate($baseroute->getRouteChildren());
        }

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($routes, $request), 200));
    }

    /**
     * Show single tenant route.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single tenant route",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/routes/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_show_routes", requirements={"id"=".+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($id)
    {
        return $this->handleView(View::create($this->findOr404($id), 200));
    }

    /**
     * Delete single tenant route.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single tenant route",
     *     statusCodes={
     *         204="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/routes/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_delete_routes", requirements={"id"=".+"})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $repository = $this->get('swp.repository.route');
        $route = $this->findOr404($id);
        $this->get('event_dispatcher')
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($route));

        if ($route->getChildren()->count() > 0) {
            throw new ConflictHttpException('Route have children routes or content attached to it.');
        }

        $repository->remove($route);

        return $this->handleView(View::create(true, 204));
    }

    /**
     * Creates routes for current tenant.
     *
     * Parameter `type` cane have one of two values: `content` or `collection`.
     *
     * Content path should be provided without tenant information:
     *
     * Instead full content path like:  ```/swp/default/content/test-content-article``` provide path like this: ```test-content-article```
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Creates routes for current tenant",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\RouteType"
     * )
     * @Route("/api/{version}/content/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_create_routes")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new RouteType(), [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            if (!isset($formData['parent']) || is_null($formData['parent'])) {
                $formData['parent'] = '/';
            }

            $route = $this->get('swp.service.route')->createRoute($formData);
            $this->get('swp.repository.route')->add($route);

            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($route));

            return $this->handleView(View::create($route, 201));
        }

        return $this->handleView(View::create($form, 200));
    }

    /**
     * Updates routes for current tenant.
     *
     * Parameter `type` cane have one of two values: `content` or `collection`.
     *
     * Content path should be provided without tenant information:
     *
     * Instead full content path like:  ```/swp/default/content/test-content-article``` provide path like this: ```test-content-article```
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates routes for current tenant",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\RouteType"
     * )
     * @Route("/api/{version}/content/routes/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_update_routes", requirements={"id"=".+"})
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $objectManager = $this->get('swp.object_manager.route');
        $route = $this->findOr404($id);
        $form = $this->createForm(new RouteType(), [
            'name' => $route->getName(),
            'type' => $route->getType(),
            'parent' => $route->getParent(),
            'content' => null !== $route->getContent() ? $route->getContent()->getId() : null,
            'template_name' => $route->getTemplateName(),
        ], ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('swp.service.route')->updateRoute($route, $form->getData());
            $objectManager->flush();

            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($route));

            return $this->handleView(View::create($route, 200));
        }

        return $this->handleView(View::create($form, 500));
    }

    private function findOr404($id)
    {
        if (null === $route = $this->get('swp.provider.route')->getOneById($id)) {
            throw new NotFoundHttpException('Route was not found.');
        }

        return $route;
    }
}
