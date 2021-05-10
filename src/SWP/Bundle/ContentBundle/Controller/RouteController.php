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
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends FOSRestController
{
    /**
     * @Route("/api/{version}/content/routes/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_routes")
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
     * @Route("/api/{version}/content/routes/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_show_routes", requirements={"id"=".+"})
     */
    public function getAction($id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * @Route("/api/{version}/content/routes/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_delete_routes", requirements={"id"=".+"})
     */
    public function deleteAction(int $id): Response
    {
        $repository = $this->get('swp.repository.route');
        $route = $this->findOr404($id);

        if (null !== $route->getContent()) {
            throw new ConflictHttpException('Route has content attached to it.');
        }

        if (0 < $route->getChildren()->count()) {
            throw new ConflictHttpException('Remove route children before removing this route.');
        }

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(new RouteEvent($route, RouteEvents::PRE_DELETE), RouteEvents::PRE_DELETE);
        $repository->remove($route);
        $eventDispatcher->dispatch(new RouteEvent($route, RouteEvents::POST_DELETE), RouteEvents::POST_DELETE);

        return $this->handleView(View::create(true, 204));
    }

    /**
     * @Route("/api/{version}/content/routes/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_create_routes")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        /** @var RouteInterface $route */
        $route = $this->get('swp.factory.route')->create();
        $form = $this->get('form.factory')->createNamed('', RouteType::class, $route, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureRouteExists($route->getName());

        if ($form->isSubmitted() && $form->isValid()) {
            $route = $this->get('swp.service.route')->createRoute($form->getData());

            $this->get('swp.repository.route')->add($route);

            return new SingleResourceResponse($route, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/content/routes/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_update_routes", requirements={"id"=".+"})
     */
    public function updateAction(Request $request, $id): Response
    {
        $objectManager = $this->get('swp.object_manager.route');
        $route = $this->findOr404($id);
        $previousRoute = clone  $route;
        $form = $this->get('form.factory')->createNamed('', RouteType::class, $route, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $route = $this->get('swp.service.route')->updateRoute($previousRoute, $form->getData());

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
