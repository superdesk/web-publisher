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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Bundle\MenuBundle\Form\Type\MenuItemMoveType;
use SWP\Bundle\MenuBundle\Form\Type\MenuType;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuController extends Controller
{
    /**
     * Lists all registered menus.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered menus",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="No menus found."
     *     }
     * )
     * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_menu")
     * @Method("GET")
     */
    public function listAction()
    {
        $menuRepository = $this->get('swp.repository.menu');

        return new ResourcesListResponse($menuRepository->findRootNodes());
    }

    /**
     * Lists all children of menu item.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all children of menu item",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="No menus found."
     *     }
     * )
     * @Route("/api/{version}/menus/{id}/children/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_children_menu")
     * @Method("GET")
     */
    public function listChildrenAction($id)
    {
        $menuRepository = $this->get('swp.repository.menu');

        $menus = $menuRepository->findChildrenAsTree($this->findOr404($id));

        return new ResourcesListResponse($menus);
    }

    /**
     * Moves menu item to a specific position.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Moves menu item to a specific position in a tree",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Menu item not found.",
     *         400="Validation error.",
     *         409="When Menu item is already placed at the same position.",
     *         500="Unexpected error."
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="An identifier of Menu item which you want to move"}
     *     },
     *     input="SWP\Bundle\MenuBundle\Form\Type\MenuItemMoveType"
     * )
     * @Route("/api/{version}/menus/{id}/move/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_move_menu", requirements={"id"="\d+"})
     * @Method("PATCH")
     */
    public function moveAction(Request $request, $id)
    {
        $menuItem = $this->findOr404($id);
        $form = $this->createForm(MenuItemMoveType::class, [], ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
     * @ApiDoc(
     *     resource=true,
     *     description="Get single menu",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Menu not found",
     *         422="Menu id is not number"
     *     }
     * )
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_get_menu")
     * @Method("GET")
     */
    public function getAction($id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Create new menu.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new menu",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\MenuBundle\Form\Type\MenuType"
     * )
     * @Route("/api/{version}/menus/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_create_menu")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $route = null;
        if (array_key_exists('route', $request->request->get('menu'))) {
            $route = $this->get('swp.repository.route')->findOneBy(['id' => $request->request->get('menu')['route']]);
        }

        /* @var MenuItemInterface $menu */
        $menu = $this->get('swp.factory.menu')->createItem('', ['route' => $route ? $route->getName() : null]);
        $form = $this->createForm(MenuType::class, $menu, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('swp.repository.menu')->add($menu);

            return new SingleResourceResponse($menu, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Delete single menu.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single menu",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Menu not found",
     *         422="Menu id is not number"
     *     }
     * )
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_menu")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $repository = $this->get('swp.repository.menu');
        $repository->remove($this->findOr404($id));

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * Update single menu.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single menu",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Menu not found",
     *         422="Menu id is not number",
     *         405="Method Not Allowed"
     *     },
     *     input="SWP\Bundle\MenuBundle\Form\Type\MenuType"
     * )
     * @Route("/api/{version}/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_menu")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $objectManager = $this->get('swp.object_manager.menu');
        $menu = $this->findOr404($id);

        $form = $this->createForm(MenuType::class, $menu, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $objectManager->flush();

            return new SingleResourceResponse($menu);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404($id)
    {
        if (null === $menu = $this->get('swp.repository.menu')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('Menu item was not found.');
        }

        return $menu;
    }
}
