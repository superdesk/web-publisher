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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\MenuBundle\Form\Type\MenuType;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuController extends FOSRestController
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
    public function listAction(Request $request)
    {
        $menuRepository = $this->get('swp.repository.menu');

        $menus = $menuRepository->getPaginatedByCriteria(new Criteria(), [], new PaginationData($request));

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($menus, $request), 200));
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
        return $this->handleView(View::create($this->findOr404($id), 200));
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

        /* @var MenuItemInterface $route */
        $menu = $this->get('swp.factory.menu')->createItem('', ['route' => $route ? $route->getName() : null]);
        $form = $this->createForm(MenuType::class, $menu, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->ensureMenuItemExists($menu->getName());

        if ($form->isValid()) {
            $this->get('swp.repository.menu')->add($menu);

            return $this->handleView(View::create($menu, 201));
        }

        return $this->handleView(View::create($form, 400));
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

        return $this->handleView(View::create(true, 204));
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
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\MenuType"
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

            return $this->handleView(View::create($menu, 200));
        }

        return $this->handleView(View::create($form, 400));
    }

    private function findOr404($id)
    {
        if (null === $menu = $this->get('swp.repository.menu')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('Menu was not found.');
        }

        return $menu;
    }

    private function ensureMenuItemExists($name)
    {
        if (null !== $this->get('swp.repository.menu')->findOneByName($name)) {
            throw new ConflictHttpException(sprintf('Menu item "%s" already exists!', $name));
        }
    }
}
