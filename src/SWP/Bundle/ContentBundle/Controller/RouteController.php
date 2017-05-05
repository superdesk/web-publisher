<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
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
     *     },
     *     filters={
     *         {"name"="type", "dataType"="string", "pattern"="collection|content"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/content/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_routes")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $routeRepository = $this->get('swp.repository.route');

        $routes = $routeRepository->getPaginatedByCriteria(new Criteria([
            'type' => $request->query->get('type', ''),
        ]), $request->query->get('sorting', []), new PaginationData($request));

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
        return new SingleResourceResponse($this->findOr404($id));
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

        if (null !== $route->getContent()) {
            throw new ConflictHttpException('Route has content attached to it.');
        }

        $repository->remove($route);

        return $this->handleView(View::create(true, 204));
    }

    /**
     * Creates routes for current tenant.
     *
     * Parameter `type` cane have one of two values: `content` or `collection`.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new route",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when not valid data."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\RouteType"
     * )
     * @Route("/api/{version}/content/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_create_routes")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        /** @var RouteInterface $route */
        $route = $this->get('swp.factory.route')->create();
        $form = $this->createForm(RouteType::class, $route, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureRouteExists($route->getName());

        if ($form->isValid()) {
            $route = $this->get('swp.service.route')->createRoute($form->getData());

            $this->get('swp.repository.route')->add($route);

            return new SingleResourceResponse($route, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Updates routes for current tenant.
     *
     * Parameter `type` cane have one of two values: `content` or `collection`.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single route",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when not valid data.",
     *         404="Returned when not found.",
     *         409="Returned on conflict."
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
        $form = $this->createForm(RouteType::class, $route, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $route = $this->get('swp.service.route')->updateRoute($form->getData());

            $objectManager->flush();

            return $this->handleView(View::create($route, 200));
        }

        return $this->handleView(View::create($form, 400));
    }

    private function findOr404($id)
    {
        if (null === $route = $this->get('swp.provider.route')->getOneById($id)) {
            throw new NotFoundHttpException('Route was not found.');
        }

        return $route;
    }

    private function ensureRouteExists($name)
    {
        if (null !== $this->get('swp.repository.route')->findOneByName($name)) {
            throw new ConflictHttpException(sprintf('Route "%s" already exists!', $name));
        }
    }
}
