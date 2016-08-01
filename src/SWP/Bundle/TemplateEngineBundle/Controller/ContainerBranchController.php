<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use SWP\Bundle\TemplateEngineBundle\Form\Type\ContainerBranchType;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerBranch;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ContainerBranchController extends FOSRestController
{
    /**
     * Create branch of container.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create branch of a container",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\ContainerBranchType"
     * )
     * @Route("/api/{version}/templates/containerbranches", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_container_branch")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $containerBranch = new ContainerBranch();
        $em = $this->get('doctrine.orm.entity_manager');
        $form = $this->createForm(new ContainerBranchType(), $containerBranch, array(
            'entity_manager' => $em,
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            // Clone container
            $containerService = $this->get('swp_template_engine_container');
            $target = $containerService->cloneContainer($containerBranch->getSource(), $form->get('target_name')->getData());
            $containerBranch->setTarget($target);
            $em->flush();
            return $this->handleView(View::create($containerBranch, 201));
        }

        return $this->handleView(View::create($form, 400));
    }


    /**
     * Publish a branched container.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Publish a branched container",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Container or branch not found",
     *         422="Container id is not number"
     *     },
     *     input="SWP\Bundle\TemplateEngineBundle\Form\Type\ContainerBranchType"
     * )
     * @Route("/api/{version}/templates/containerbranches/publish/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_templates_create_container_branch_publish")
     * @Method("POST")
     */
    public function publishAction(Request $request, $containerId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $container = $em->getRepository('SWP\Bundle\TemplateEngineBundle\Model\Container')
            ->getById($containerId)
            ->getOneOrNullResult();

        if (!$container) {
            throw new NotFoundHttpException('Container with this id was not found.');
        }

        /** @var ContainerBranch $branch */
        $branch = $em->getRepository('SWP\Bundle\TemplateEngineBundle\Model\ContainerBranch')
            ->findOneBy(['target_id' => $containerId])
            ->getOneOrNullResult();

        if (!$branch) {
            throw new NotFoundHttpException('Branch not found.');
        }

        $source = $branch->getSource();
        $containerService = $this->get('swp_template_engine_container');
    }
}
