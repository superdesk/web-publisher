<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RuleController extends FOSRestController
{
    /**
     * List all Rule entities for current tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all rules",
     *     statusCodes={
     *         200="Returned on success.",
     *         405="Method Not Allowed."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/rules/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_rule")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $rules = $this->get('swp.repository.rule')
            ->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        if (0 === $rules->count()) {
            throw new NotFoundHttpException('No rules were found.');
        }

        return new ResourcesListResponse($rules);
    }

    /**
     * Get single Rule Entity.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Get single rule",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Rule not found.",
     *         405="Method Not Allowed."
     *     }
     * )
     * @Route("/api/{version}/rules/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_get_rule")
     * @Method("GET")
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(RuleInterface $rule)
    {
        return new SingleResourceResponse($rule);
    }

    /**
     * Create new Rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new rule",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\RuleBundle\Form\Type\RuleType"
     * )
     * @Route("/api/{version}/rules/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_create_rule")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $ruleRepository = $this->get('swp.repository.rule');

        $rule = $this->get('swp.factory.rule')->create();
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ruleRepository->add($rule);

            return new SingleResourceResponse($rule, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Delete single rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single rule",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Returned when rule not found.",
     *         405="Returned when method not allowed."
     *     }
     * )
     * @Route("/api/{version}/rules/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_rule", requirements={"id"="\d+"})
     * @Method("DELETE")
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function deleteAction(RuleInterface $rule)
    {
        $ruleRepository = $this->get('swp.repository.rule');
        $ruleRepository->remove($rule);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * Updates single rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single rule",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         404="Rule not found.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\RuleBundle\Form\Type\RuleType"
     * )
     * @Route("/api/{version}/rules/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_rule", requirements={"id"="\d+"})
     * @Method("PATCH")
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function updateAction(Request $request, RuleInterface $rule)
    {
        $objectManager = $this->get('swp.object_manager.rule');

        $form = $this->createForm(RuleType::class, $rule, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($rule);

            return new SingleResourceResponse($rule);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
