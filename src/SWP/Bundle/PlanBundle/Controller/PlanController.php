<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Plan Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PlanBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\PlanBundle\Form\Type\PlanType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlanController extends Controller
{
    /**
     * Create a new Pricing Plan.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create a new plan",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\PlanBundle\Form\Type\PlanType"
     * )
     * @Route("/api/{version}/plans/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_plan_create")
     *
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return SingleResourceResponseInterface
     */
    public function create(Request $request): SingleResourceResponseInterface
    {
        $planRepository = $this->get('swp.repository.plan');
        $planFactory = $this->get('swp.factory.plan');
        $formFactory = $this->get('form.factory');

        $plan = $planFactory->create();
        $form = $formFactory->create(PlanType::class, $plan);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $planRepository->add($plan);

            return new SingleResourceResponse($plan, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
