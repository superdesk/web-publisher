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
use SWP\Bundle\ContentBundle\Document;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoutesController extends FOSRestController
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
        $manager = $this->get('doctrine_phpcr')->getManager();
        $basepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')[0]);
        $baseroute = $manager->find('SWP\Bundle\ContentBundle\Document\Route', $basepath);
        $routes = [];

        if ($baseroute) {
            $routes = $this->get('knp_paginator')->paginate($baseroute->getRouteChildren());
        }

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($routes, $request), 200));
    }

    /**
     * Show single tenenat route.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single tenenat route",
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
        $manager = $this->get('doctrine_phpcr')->getManager();
        $routeBasepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.base_paths')[0]);
        $route = $manager->find('SWP\Bundle\ContentBundle\Document\Route', $routeBasepath.$id);

        if (!$route) {
            throw new NotFoundHttpException('Route was not found.');
        }

        return $this->handleView(View::create($route, 200));
    }

    /**
     * Delete single tenenat route.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single tenenat route",
     *     statusCodes={
     *         204="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/routes/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_delete_routes", requirements={"id"=".+"})
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $manager = $this->get('doctrine_phpcr')->getManager();
        $routeBasepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.base_paths')[0]);
        $route = $manager->find('SWP\Bundle\ContentBundle\Document\Route', $routeBasepath.$id);

        if (!$route) {
            throw new NotFoundHttpException('Route was not found.');
        }

        $this->get('event_dispatcher')
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($route));

        if ($route->getChildren()->count() > 0) {
            throw new ConflictHttpException('Route have children routes or content attached to it.');
        }

        $manager->remove($route);
        $manager->flush();

        return $this->handleView(View::create(true, 204));
    }

    /**
     * Creates routes for current tenant.
     *
     * Parameter `type` cane have one of two values: `content` or `collection`.
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
        $manager = $this->get('doctrine_phpcr')->getManager();

        $form = $this->createForm(new RouteType(), [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            if (!isset($formData['parent']) || is_null($formData['parent'])) {
                $formData['parent'] = '/';
            }

            $route = $this->handleRouteUpdate(new Document\Route(), $formData);
            $manager->persist($route);
            $manager->flush();

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
        $manager = $this->get('doctrine_phpcr')->getManager();
        $routeBasepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.base_paths')[0]);
        $route = $manager->find('SWP\Bundle\ContentBundle\Document\Route', $routeBasepath.$id);
        if (!$route) {
            throw new NotFoundHttpException('Route was not found.');
        }

        $form = $this->createForm(new RouteType(), [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($route));

            $route = $this->handleRouteUpdate($route, $form->getData());
            $manager->flush();

            return $this->handleView(View::create($route, 200));
        }

        return $this->handleView(View::create($form, 200));
    }

    private function handleRouteUpdate($route, $routeData)
    {
        $manager = $this->get('doctrine_phpcr')->getManager();
        $basepaths = $this->getParameter('swp_multi_tenancy.persistence.phpcr.base_paths');

        if (isset($routeData['parent'])) {
            $routeBasepath = $this->get('swp_multi_tenancy.path_builder')->build($basepaths[0]);
            if (!is_null($routeData['parent']) && $routeData['parent'] !== '/') {
                $parentRoute = $manager->find('SWP\Bundle\ContentBundle\Document\Route', $routeBasepath.$routeData['parent']);

                if ($parentRoute) {
                    $route->setParentDocument($parentRoute);
                }
            } else {
                $route->setParentDocument($manager->find('SWP\Bundle\ContentBundle\Document\Route', $routeBasepath));
            }
        }

        if (isset($routeData['content']) && !is_null($routeData['content'])) {
            $contentBasepath = $this->get('swp_multi_tenancy.path_builder')->build($basepaths[1]);
            $routeContent = $manager->find('SWP\Bundle\ContentBundle\Document\Article', $contentBasepath.$routeData['content']);

            if ($routeContent) {
                $route->setContent($routeContent);
            }
        }

        if (isset($routeData['name'])) {
            $route->setName($routeData['name']);
        }

        if (isset($routeData['type']) && $routeData['type'] == Document\Route::TYPE_CONTENT) {
            $route->setDefault('_controller', '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContentPageAction');
            $route->setVariablePattern(null);
            $route->setRequirements([]);
        } elseif (isset($routeData['type'])) {
            $route->setDefault('_controller', '\SWP\Bundle\WebRendererBundle\Controller\ContentController::renderContainerPageAction');
            $route->setVariablePattern('/{slug}');
            $route->setRequirement('slug', '[a-zA-Z1-9\-_\/]+');
        }

        return $route;
    }
}
