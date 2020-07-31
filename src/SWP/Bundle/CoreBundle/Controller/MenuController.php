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

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\MenuBundle\MenuEvents;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Bundle\MenuBundle\Form\Type\MenuItemMoveType;
use SWP\Bundle\MenuBundle\Form\Type\MenuType;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuController extends AbstractController
{
    /**
     * Lists all registered menus.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Lists all registered menus",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="No menus found."
     *     )
     * )
     *
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
     * Lists all children of menu item.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Lists all children of menu item",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="No menus found."
     *     )
     * )
     *
     * @Route("/api/{version}/menus/{id}/children/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_children_menu")
     */
    public function listChildrenAction($id): ResourcesListResponseInterface
    {
        $menuRepository = $this->get('swp.repository.menu');

        $menus = $menuRepository->findChildrenAsTree($this->findOr404($id));

        return new ResourcesListResponse($menus);
    }

    /**
     * Moves menu item to a specific position.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Moves menu item to a specific position in a tree",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=MenuItemMoveType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Menu item not found."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation error."
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="When Menu item is already placed at the same position."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
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
     * Get single menu.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Get single menu",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Menu not found"
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Menu id is not number"
     *     )
     * )
     *
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_menu")
     */
    public function getAction($id): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Create new menu.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Create new menu",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=MenuType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when form have errors"
     *     )
     * )
     *
     *
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
            $this->get('event_dispatcher')->dispatch(MenuEvents::MENU_CREATED, new GenericEvent($menu));

            return new SingleResourceResponse($menu, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Delete single menu.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Delete single menu",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Menu not found"
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Menu id is not number"
     *     )
     * )
     *
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_menu")
     */
    public function deleteAction(int $id)
    {
        $repository = $this->get('swp.repository.menu');
        $menu = $this->findOr404($id);

        $repository->remove($menu);
        $this->get('event_dispatcher')->dispatch(MenuEvents::MENU_DELETED, new GenericEvent($menu));

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * Update single menu.
     *
     * @Operation(
     *     tags={"menu"},
     *     summary="Update single menu",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=MenuType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\MenuItem::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Menu not found"
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Menu id is not number"
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed"
     *     )
     * )
     *
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

            $this->get('event_dispatcher')->dispatch(MenuEvents::MENU_UPDATED, new GenericEvent($menu));

            return new SingleResourceResponse($menu);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404($id): MenuItemInterface
    {
        if (null === $menu = $this->get('swp.repository.menu')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('Menu item was not found.');
        }

        return $menu;
    }
}
