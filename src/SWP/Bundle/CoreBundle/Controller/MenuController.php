<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Manager\MenuItemManager;
use SWP\Bundle\CoreBundle\Repository\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Factory\MenuFactoryInterface;
use SWP\Bundle\MenuBundle\Form\Type\MenuItemMoveType;
use SWP\Bundle\MenuBundle\Form\Type\MenuType;
use SWP\Bundle\MenuBundle\MenuEvents;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController {

  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;
  private MenuItemRepositoryInterface $menuItemRepository;
  private MenuItemManager $menuItemManager;
  private MenuFactoryInterface $menuFactory;
  private EntityManagerInterface $entityManager;

  /**
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   * @param MenuItemRepositoryInterface $menuItemRepository
   * @param MenuItemManager $menuItemManager
   * @param MenuFactoryInterface $menuFactory
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(FormFactoryInterface        $formFactory, EventDispatcherInterface $eventDispatcher,
                              MenuItemRepositoryInterface $menuItemRepository, MenuItemManager $menuItemManager,
                              MenuFactoryInterface        $menuFactory, EntityManagerInterface $entityManager) {
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
    $this->menuItemRepository = $menuItemRepository;
    $this->menuItemManager = $menuItemManager;
    $this->menuFactory = $menuFactory;
    $this->entityManager = $entityManager;
  }


  /**
   * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_menu")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    $menuRepository = $this->menuItemRepository;
    $page = $request->query->get('page', 1);
    $limit = $request->query->get('limit', 10);

    return new ResourcesListResponse($menuRepository->findRootNodes($this->eventDispatcher, $page, $limit));
  }

  /**
   * @Route("/api/{version}/menus/{id}/children/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_children_menu")
   */
  public function listChildrenAction($id): ResourcesListResponseInterface {
    $menuRepository = $this->menuItemRepository;

    $menus = $menuRepository->findChildrenAsTree($this->eventDispatcher, $this->findOr404($id));

    return new ResourcesListResponse($menus);
  }

  /**
   * @Route("/api/{version}/menus/{id}/move/", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_move_menu", requirements={"id"="\d+"})
   */
  public function moveAction(Request $request, $id): SingleResourceResponseInterface {
    $menuItem = $this->findOr404($id);
    $form = $this->formFactory->createNamed('', MenuItemMoveType::class, [], ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $menuItemManager = $this->menuItemManager;
      $formData = $form->getData();

      $menuItemManager->move($menuItem, $formData['parent'], $formData['position']);

      return new SingleResourceResponse($menuItem);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_menu")
   */
  public function getAction($id): SingleResourceResponseInterface {
    return new SingleResourceResponse($this->findOr404($id));
  }

  /**
   * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_menu")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    /* @var MenuItemInterface $menu */
    $menu = $this->menuFactory->create();
    $form = $this->formFactory->createNamed('', MenuType::class, $menu, ['method' => $request->getMethod()]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->menuItemManager->update($menu);
      $this->menuItemRepository->add($menu);
      $this->eventDispatcher->dispatch(new GenericEvent($menu), MenuEvents::MENU_CREATED);

      return new SingleResourceResponse($menu, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_menu")
   */
  public function deleteAction(int $id) {
    $repository = $this->menuItemRepository;
    $menu = $this->findOr404($id);

    $repository->remove($menu);
    $this->eventDispatcher->dispatch(new GenericEvent($menu), MenuEvents::MENU_DELETED);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  /**
   * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_menu")
   */
  public function updateAction(Request $request, $id): SingleResourceResponseInterface {
    $objectManager = $this->entityManager;
    $menu = $this->findOr404($id);

    $form = $this->formFactory->createNamed('', MenuType::class, $menu, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->menuItemManager->update($menu);
      $objectManager->flush();

      $this->eventDispatcher->dispatch(new GenericEvent($menu), MenuEvents::MENU_UPDATED);

      return new SingleResourceResponse($menu);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  private function findOr404($id): MenuItemInterface {
    /* @var MenuItemInterface $menu */
    if (null === $menu = $this->menuItemRepository->findOneBy(['id' => $id])) {
      throw new NotFoundHttpException('Menu item was not found.');
    }

    return $menu;
  }
}
