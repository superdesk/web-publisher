<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\RedirectRouteBundle\Form\RedirectRouteType;
use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectRouteController extends AbstractController {

  private FormFactoryInterface $formFactory;
  private RepositoryInterface $redirectRouteRepository;
  private EntityManagerInterface $entityManager;
  private FactoryInterface $redirectRouteFactory;

  /**
   * @param FormFactoryInterface $formFactory
   * @param RepositoryInterface $redirectRouteRepository
   * @param EntityManagerInterface $entityManager
   * @param FactoryInterface $redirectRouteFactory
   */
  public function __construct(FormFactoryInterface   $formFactory, RepositoryInterface $redirectRouteRepository,
                              EntityManagerInterface $entityManager, FactoryInterface $redirectRouteFactory) {
    $this->formFactory = $formFactory;
    $this->redirectRouteRepository = $redirectRouteRepository;
    $this->entityManager = $entityManager;
    $this->redirectRouteFactory = $redirectRouteFactory;
  }


  /**
   * @Route("/api/{version}/redirects/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_redirect_routes")
   */
  public function listAction(Request $request) {
    $redirectRouteRepository = $this->redirectRouteRepository;

    $redirectRoutes = $redirectRouteRepository->getPaginatedByCriteria(new Criteria(), $request->query->all('sorting'), new PaginationData($request));

    return new ResourcesListResponse($redirectRoutes);
  }

  /**
   * @Route("/api/{version}/redirects/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_delete_redirect_route", requirements={"id"="\d+"})
   */
  public function deleteAction(int $id): SingleResourceResponseInterface {
    $objectManager = $this->entityManager;
    $redirectRoute = $this->findOr404($id);

    $objectManager->remove($redirectRoute);
    $objectManager->flush();

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  /**
   * @Route("/api/{version}/redirects/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_create_redirect_route")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    $redirectRoute = $this->redirectRouteFactory->create();
    $form = $this->formFactory->createNamed('', RedirectRouteType::class, $redirectRoute, ['method' => $request->getMethod()]);

    $form->handleRequest($request);

    if ($this->checkIfSourceRouteExists($redirectRoute)) {
      $this->ensureRedirectRouteExists($redirectRoute->getRouteName());
    }

    if ($form->isSubmitted() && $form->isValid()) {
      if ($this->checkIfSourceRouteExists($redirectRoute)) {
        $redirectRoute->setStaticPrefix($redirectRoute->getRouteName());
      } else {
        $redirectRoute->setStaticPrefix($redirectRoute->getRouteSource()->getStaticPrefix());
      }

      $this->redirectRouteRepository->add($redirectRoute);

      return new SingleResourceResponse($redirectRoute, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/redirects/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_update_redirect_route", requirements={"id"="\d+"})
   */
  public function updateAction(Request $request, int $id): SingleResourceResponseInterface {
    $objectManager = $this->entityManager;
    $redirectRoute = $this->findOr404($id);
    $form = $this->formFactory->createNamed('', RedirectRouteType::class, $redirectRoute, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $objectManager->flush();

      return new SingleResourceResponse($redirectRoute, new ResponseContext(200));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  private function findOr404(int $id): RedirectRouteInterface {
    if (null === $redirectRoute = $this->redirectRouteRepository->findOneById($id)) {
      throw new NotFoundHttpException('Redirect route was not found.');
    }

    return $redirectRoute;
  }

  private function ensureRedirectRouteExists(?string $name): void {
    if (null !== $this->redirectRouteRepository->findOneBy(['routeName' => $name])) {
      throw new ConflictHttpException(sprintf('Redirect route "%s" already exists!', $name));
    }
  }

  private function checkIfSourceRouteExists(RedirectRouteInterface $redirectRoute): bool {
    return null === $redirectRoute->getRouteSource();
  }
}
