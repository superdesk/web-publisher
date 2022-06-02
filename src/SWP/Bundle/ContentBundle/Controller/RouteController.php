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

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController as FOSRestController;
use FOS\RestBundle\View\View;
use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Factory\RouteFactory;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Form\Type\RouteType;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Factory\KnpPaginatorRepresentationFactory;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends FOSRestController {

  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;
  private RouteProviderInterface $routeProvider; // swp.provider.route
  private RouteRepositoryInterface $routeRepository; // swp.repository.route
  private RouteServiceInterface $routeService; // swp.service.route
  private RouteFactoryInterface $routeFactory; // swp.factory.route
  private KnpPaginatorRepresentationFactory $knpPaginatorRepresentationFactory; //swp_pagination_rep
  private EntityManagerInterface $entityManager; // swp.object_manager.route

  /**
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   * @param RouteProviderInterface $routeProvider
   * @param RouteRepositoryInterface $routeRepository
   * @param RouteServiceInterface $routeService
   * @param RouteFactoryInterface $routeFactory
   * @param KnpPaginatorRepresentationFactory $knpPaginatorRepresentationFactory
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(FormFactoryInterface              $formFactory, EventDispatcherInterface $eventDispatcher,
                              RouteProviderInterface            $routeProvider,
                              RouteRepositoryInterface          $routeRepository, RouteServiceInterface $routeService,
                              RouteFactoryInterface             $routeFactory,
                              KnpPaginatorRepresentationFactory $knpPaginatorRepresentationFactory,
                              EntityManagerInterface            $entityManager) {
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
    $this->routeProvider = $routeProvider;
    $this->routeRepository = $routeRepository;
    $this->routeService = $routeService;
    $this->routeFactory = $routeFactory;
    $this->knpPaginatorRepresentationFactory = $knpPaginatorRepresentationFactory;
    $this->entityManager = $entityManager;
  }


  /**
   * @Route("/api/{version}/content/routes/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_routes")
   */
  public function listAction(Request $request) {
    $routeRepository = $this->routeRepository;

    $routes = $routeRepository->getPaginatedByCriteria(new Criteria([
        'type' => $request->query->get('type', ''),
    ]), $request->query->all('sorting'), new PaginationData($request));

    return $this->handleView(View::create($this->knpPaginatorRepresentationFactory->createRepresentation($routes, $request), 200));
  }

  /**
   * @Route("/api/{version}/content/routes/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_show_routes", requirements={"id"=".+"})
   */
  public function getAction($id) {
    return new SingleResourceResponse($this->findOr404($id));
  }

  /**
   * @Route("/api/{version}/content/routes/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_delete_routes", requirements={"id"=".+"})
   */
  public function deleteAction(int $id): Response {
    $repository = $this->routeRepository;
    $route = $this->findOr404($id);

    if (null !== $route->getContent()) {
      throw new ConflictHttpException('Route has content attached to it.');
    }

    if (0 < $route->getChildren()->count()) {
      throw new ConflictHttpException('Remove route children before removing this route.');
    }

    $eventDispatcher = $this->eventDispatcher;
    $eventDispatcher->dispatch(new RouteEvent($route, RouteEvents::PRE_DELETE), RouteEvents::PRE_DELETE);
    $repository->remove($route);
    $eventDispatcher->dispatch(new RouteEvent($route, RouteEvents::POST_DELETE), RouteEvents::POST_DELETE);

    return $this->handleView(View::create(true, 204));
  }

  /**
   * @Route("/api/{version}/content/routes/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_create_routes")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    /** @var RouteInterface $route */
    $route = $this->routeFactory->create();
    $form = $this->formFactory->createNamed('', RouteType::class, $route, ['method' => $request->getMethod()]);

    $form->handleRequest($request);
    $this->ensureRouteExists($route->getName());

    if ($form->isSubmitted() && $form->isValid()) {
      $route = $this->routeService->createRoute($form->getData());

      $this->routeRepository->add($route);

      return new SingleResourceResponse($route, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/content/routes/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_update_routes", requirements={"id"=".+"})
   */
  public function updateAction(Request $request, $id): Response {
    $objectManager = $this->entityManager;
    $route = $this->findOr404($id);
    $previousRoute = clone $route;
    $form = $this->formFactory->createNamed('', RouteType::class, $route, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $route = $this->routeService->updateRoute($previousRoute, $form->getData());

      $objectManager->flush();

      return $this->handleView(View::create($route, 200));
    }

    return $this->handleView(View::create($form, 400));
  }

  private function findOr404($id) {
    if (null === $route = $this->routeProvider->getOneById($id)) {
      throw new NotFoundHttpException('Route was not found.');
    }

    return $route;
  }

  private function ensureRouteExists($name) {
    if (null !== $this->routeRepository->findOneByName($name)) {
      throw new ConflictHttpException(sprintf('Route "%s" already exists!', $name));
    }
  }
}
