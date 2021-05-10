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
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_menu")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $menuRepository = $this->get('swp.repository.menu');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        return new ResourcesListResponse($menuRepository->findRootNodes($page, $limit));
    }

    /**
     * @Route("/api/{version}/menus/{id}/children/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_children_menu")
     */
    public function listChildrenAction($id): ResourcesListResponseInterface
    {
        $menuRepository = $this->get('swp.repository.menu');

        $menus = $menuRepository->findChildrenAsTree($this->findOr404($id));

        return new ResourcesListResponse($menus);
    }

    /**
     * @Route("/api/{version}/menus/{id}/move/", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_move_menu", requirements={"id"="\d+"})
     */
    public function moveAction(Request $request, $id): SingleResourceResponseInterface
    {
        $menuItem = $this->findOr404($id);
        $form = $this->get('form.factory')->createNamed('', MenuItemMoveType::class, [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuItemManager = $this->get('swp_menu.manager.menu_item');
            $formData = $form->getData();

            $menuItemManager->move($menuItem, $formData['parent'], $formData['position']);

            return new SingleResourceResponse($menuItem);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_menu")
     */
    public function getAction($id): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_menu")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        /* @var MenuItemInterface $menu */
        $menu = $this->get('swp.factory.menu')->create();
        $form = $this->get('form.factory')->createNamed('', MenuType::class, $menu, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swp_menu.manager.menu_item')->update($menu);
            $this->get('swp.repository.menu')->add($menu);
            $this->get('event_dispatcher')->dispatch(new GenericEvent($menu), MenuEvents::MENU_CREATED);

            return new SingleResourceResponse($menu, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_menu")
     */
    public function deleteAction(int $id)
    {
        $repository = $this->get('swp.repository.menu');
        $menu = $this->findOr404($id);

        $repository->remove($menu);
        $this->get('event_dispatcher')->dispatch(new GenericEvent($menu), MenuEvents::MENU_DELETED);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_menu")
     */
    public function updateAction(Request $request, $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.menu');
        $menu = $this->findOr404($id);

        $form = $this->get('form.factory')->createNamed('', MenuType::class, $menu, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swp_menu.manager.menu_item')->update($menu);
            $objectManager->flush();

            $this->get('event_dispatcher')->dispatch(new GenericEvent($menu), MenuEvents::MENU_UPDATED);

            return new SingleResourceResponse($menu);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404($id): MenuItemInterface
    {
        /* @var MenuItemInterface $menu */
        if (null === $menu = $this->get('swp.repository.menu')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('Menu item was not found.');
        }

        return $menu;
    }
}
