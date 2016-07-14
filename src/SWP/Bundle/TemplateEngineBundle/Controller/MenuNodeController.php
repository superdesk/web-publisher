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
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\TemplateEngineBundle\Provider\TenantAwareMenuProvider;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

class MenuNodeController extends FOSRestController
{
    /**
     * Lists all registered menus.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered menu nodes of a given menu",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="No menu nodes found."
     *     }
     * )
     * @Route("/api/{version}/templates/menunodes/{menuId}/{nodeId}", requirements={"nodeId"=".+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list_menu_nodes")
     * @Method("GET")
     */
    public function listAction(Request $request, $menuId, $nodeId)
    {
        if (!$menuId) {
            throw new UnprocessableEntityHttpException('You need to provide menu name (name).');
        }

        /** @var DocumentManager $dm */
        $dm = $this->get('document_manager');

        /** @var QueryBuilder $qb */
        $qb = $dm->createQueryBuilder();
        $qb->from()->document('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', 'm');

        $path = $this->getBaseDocumentPath().'/'.$menuId;
        if ($nodeId) {
            $path .= '/'.$nodeId;
        }

        $qb->where()->descendant($path, 'm');
        $query = $qb->getQuery();

        $paginator = $this->get('knp_paginator');
        $menuNodes = $paginator->paginate($query);

        if (count($menuNodes) == 0) {
            throw new NotFoundHttpException('Menu nodes were not found.');
        }

        return $this->handleView(View::create($this->container->get('swp_pagination_rep')->createRepresentation($menuNodes, $request), 200));
    }

    /**
     * Get single menu node.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single menu",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Menu not found",
     *         422="No id given"
     *     }
     * )
     * @Route("/api/{version}/templates/menunodes/{menuId}/{nodeId}", requirements={"nodeId"=".+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get_menu_node")
     * @Method("GET")
     */
    public function getAction(Request $request, $menuId, $nodeId)
    {
        $menuNode = $this->getMenuNode($menuId, $nodeId);
        if (!$menuNode) {
            throw new NotFoundHttpException('Menu with this id was not found.');
        }

        return $this->handleView(View::create($menuNode, 200));
    }

    /**
     * Create new menu node.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new menu node",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors",
     *         422="Menu not found",
     *         405="Method Not Allowed"
     *     },
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\MenuNodeType"
     * )
     * @Route("/api/{version}/templates/menunodes/{menuId}/{nodeId}", requirements={"nodeId"=".+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_menu_node")
     * @Method("POST")
     */
    public function createAction(Request $request, $menuId, $nodeId)
    {
        $menuNode = new MenuNode();
        $form = $this->createForm(new MenuType(), $menuNode);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if (!$menuId) {
                throw new UnprocessableEntityHttpException('You need to provide menu Id (name).');
            }

            /** @var DocumentManager $dm */
            $dm = $this->get('document_manager');
            $path = $this->getBaseDocumentPath().'/'.$menuId;
            if ($nodeId) {
                $path .= '/'.$nodeId;
            }
            $menuParent = $dm->find(null, $path);
            if (!$menuParent) {
                throw new NotFoundHttpException('Menu with given id was not found.');
            }
            $menuNode->setParentDocument($menuParent);
            $dm->persist($menuNode);
            $dm->flush();

            return $this->handleView(View::create($menuNode, 201));
        }

        return $this->handleView(View::create($form, 400));
    }

    /**
     * Delete single menu node.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single menu node",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Menu or node not found",
     *         422="Menu id is not number"
     *     }
     * )
     * @Route("/api/{version}/templates/menunodes/{menuId}/{nodeId}", requirements={"nodeId"=".+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_delete_menu_node")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $menuId, $nodeId)
    {
        $menuNode = $this->getMenuNode($menuId, $nodeId);
        if (!$menuNode) {
            throw new NotFoundHttpException('Menu node with this id was not found.');
        }

        /** @var DocumentManager $dm */
        $dm = $this->get('document_manager');
        $dm->remove($menuNode);
        $dm->flush();

        return $this->handleView(View::create(null, 204));
    }

    /**
     * Update single menu node.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single menu",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Menu or node not found",
     *         422="Menu id is not number",
     *         405="Method Not Allowed"
     *     },
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\MenuNodeType"
     * )
     * @Route("/api/{version}/templates/menunodes/{menuId}/{nodeId}", requirements={"nodeId"=".+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update_menu_node")
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $menuId, $nodeId)
    {
        $menuNode = $this->getMenuNode($menuId, $nodeId);
        if (!$menuNode) {
            throw new NotFoundHttpException('Menu node with given id was not found.');
        }

        $form = $this->createForm(new MenuNodeType(), $menuNode, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var DocumentManager $dm */
            $dm = $this->get('document_manager');
            $dm->flush();

            return $this->handleView(View::create($menuNode, 201));
        }

        return $this->handleView(View::create($form, 200));
    }

    /**
     * @param $menuId
     * @param $id
     * @return Menu
     * @throws UnprocessableEntityHttpException
     */
    private function getMenuNode($menuId, $nodeId)
    {
        if (!$menuId) {
            throw new UnprocessableEntityHttpException('You need to provide menu Id (name).');
        }

        if (!$nodeId) {
            throw new UnprocessableEntityHttpException('You need to provide menu node Id (name).');
        }

        /** @var DocumentManager $dm */
        $dm = $this->get('document_manager');
        $id = $this->getBaseDocumentPath().'/'.$menuId.'/'.$nodeId;
        $menuNode = $dm->find('Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode', $id);

        return $menuNode;
    }

    /**
     * Returns base document path of menus for this tenant
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
