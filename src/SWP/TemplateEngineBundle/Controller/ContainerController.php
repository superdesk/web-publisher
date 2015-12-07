<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\TemplateEngineBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use SWP\ContentBundle\Factory\KnpPaginatorRepresentationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\TemplateEngineBundle\Form\Type\ContainerType;

class ContainerController extends FOSRestController
{
    /**
     * Lists all registered containers
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all registered containers",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/templates/containers/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_list")
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->get('doctrine')->getManager();
        $paginator = $this->get('knp_paginator');
        $containers = $paginator->paginate($entityManager->getRepository('SWP\TemplateEngineBundle\Model\Container')->getAll());
    
        if (count($containers) == 0) {
            throw new NotFoundHttpException('Containers were not found.');
        }

        return $this->container->get('swp_pagination_rep')
            ->createRepresentation($containers, $request, 'containers');
    }

    /**
     * Get single container
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single container",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Container not found",
     *         422="Container id is not number"
     *     }
     * )
     * @Route("/api/{version}/templates/containers/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_get")
     * @Method("GET")
     * @Rest\View(statusCode=200)
     */
    public function getAction(Request $request, $id)
    {
        if (!$id || !is_numeric($id)) {
            throw new UnprocessableEntityHttpException('You need to provide container Id (integer).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $container = $entityManager->getRepository('SWP\TemplateEngineBundle\Model\Container')->getById($id)->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        return $container;
    }

    /**
     * Update single container
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single container",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Container not found",
     *         422="Container id is not number"
     *     },
     *     input="SWP\TemplateEngineBundle\Form\Type\ContainerType"
     * )
     * @Route("/api/{version}/templates/containers/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_update")
     * @Method("PATCH|PUT")
     * @Rest\View(statusCode=200)
     */
    public function updateAction(Request $request, $id)
    {
        if (!$id || !is_numeric($id)) {
            throw new UnprocessableEntityHttpException('You need to provide container Id (integer).');
        }

        $entityManager = $this->get('doctrine')->getManager();
        $container = $entityManager->getRepository('SWP\TemplateEngineBundle\Model\Container')->getById($id)->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        $form = $this->createForm(new ContainerType(), $container, array(
            'method' => $request->getMethod(),
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager->flush($container);

            return $container;
        }

        return $form;
    }
}
