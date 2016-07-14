<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Controller;

use Doctrine\ODM\PHPCR\DocumentManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Component\HttpFoundation\Request;
use SWP\Bundle\TemplateEngineBundle\Form\Type\MenuType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class MenuController extends FOSRestController
{
    /**
     * Lists all registered menus.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered menus",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/menus/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_menus")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $menuParent = $this->getMenuParent();
        $dm = $this->get('document_manager');
        $menus = $dm->getChildren($menuParent);
        $paginator = $this->get('knp_paginator');
        $menus = $paginator->paginate($menus);

        if (count($menus) == 0) {
            throw new NotFoundHttpException('Menus were not found.');
        }

        return $this->handleView(View::create($this->container->get('swp_pagination_rep')->createRepresentation($menus, $request), 200));
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
     * @Route("/api/{version}/templates/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_menu")
     * @Method("GET")
     */
    public function getAction(Request $request, $id)
    {
        $menu = $this->getMenu($id);
        if (!$menu) {
            throw new NotFoundHttpException('Menu with this id was not found.');
        }

        return $this->handleView(View::create($menu, 200));
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
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\MenuType"
     * )
     * @Route("/api/{version}/templates/menus", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_menu")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $menu = new Menu();

        // Parent must be set before validation
        $menuParent = $this->getMenuParent();
        $menu->setParentDocument($menuParent);

        $form = $this->createForm(new MenuType(), $menu);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var DocumentManager $dm */
            $dm = $this->get('document_manager');
            $dm->persist($menu);
            $dm->flush();

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
     * @Route("/api/{version}/templates/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_delete_menu")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $menu = $this->getMenu($id);
        if (!$menu) {
            throw new NotFoundHttpException('Menu with this id was not found.');
        }

        /** @var DocumentManager $dm */
        $dm = $this->get('document_manager');
        $dm->remove($menu);
        $dm->flush();

        return $this->handleView(View::create(null, 204));
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
     * @Route("/api/{version}/templates/menus/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_menu")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $menu = $this->getMenu($id);
        if (!$menu) {
            throw new NotFoundHttpException('Menu with this id was not found.');
        }

        $form = $this->createForm(new MenuType(), $menu, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var DocumentManager $dm */
            $dm = $this->get('document_manager');
            $dm->flush();

            $menu = $this->getMenu($id);

            return $this->handleView(View::create($menu, 201));
        }

        return $this->handleView(View::create($form, 200));
    }

    /**
     * @return null|object
     */
    private function getMenuParent()
    {
        $dm = $this->get('document_manager');
        $menuParent = $dm->find(null, $this->getBaseDocumentPath());

        return $menuParent;
    }

    /**
     * @param $id
     *
     * @return Menu
     *
     * @throws UnprocessableEntityHttpException
     */
    private function getMenu($id)
    {
        if (!$id) {
            throw new UnprocessableEntityHttpException('You need to provide menu Id (name).');
        }

        /** @var DocumentManager $dm */
        $dm = $this->get('document_manager');
        $id = $this->getBaseDocumentPath().'/'.$id;
        $menu = $dm->find('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu', $id);

        return $menu;
    }

    /**
     * Returns base document path of menus for this tenant.
     *
     * @return string
     */
    private function getBaseDocumentPath()
    {
        $mp = $this->get('swp_template_engine.menu_provider');
        $path = $mp->getMenuRoot();

        return $path;
    }
}
