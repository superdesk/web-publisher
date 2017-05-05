<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrganizationRuleController extends Controller
{
    /**
     * List all current organization's rules.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all current organization's articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_organization_rules")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function rulesAction(Request $request)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $this->getEventDispatcher()->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $repository = $this->getRuleRepository();
        $rules = $repository->getPaginatedByCriteria(
            new Criteria([
                'organization' => $tenantContext->getTenant()->getOrganization(),
                'tenantCode' => null,
            ]),
            $request->query->get('sorting', []),
            new PaginationData($request)
        );

        return new ResourcesListResponse($rules);
    }

    /**
     * Create a new Organization Rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create a new organization rule",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on validation error.",
     *         405="Method Not Allowed."
     *     },
     *     input="SWP\Bundle\RuleBundle\Form\Type\RuleType"
     * )
     * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_create_organization_rule")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $ruleRepository = $this->getRuleRepository();

        $rule = $this->get('swp.factory.rule')->create();
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ruleRepository->add($rule);
            $rule->setTenantCode(null);
            $ruleRepository->flush();

            return new SingleResourceResponse($rule, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Show single organiation's rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single organization rule",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_organization_rule", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(int $id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Updates organization's rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates organization rule",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\RuleType"
     * )
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_organization_rule", requirements={"id"="\d+"})
     * @Method("PATCH")
     */
    public function updateRuleAction(Request $request, int $id)
    {
        $objectManager = $this->get('swp.object_manager.rule');
        $rule = $this->findOr404($id);
        $form = $this->createForm(RuleType::class, $rule, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($rule);

            return new SingleResourceResponse($rule);
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * Delete single organization rule.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single organization rule",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Returned when rule not found.",
     *         405="Returned when method not allowed."
     *     }
     * )
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_organization_rule", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(int $id)
    {
        $rule = $this->findOr404($id);
        $ruleRepository = $this->get('swp.repository.rule');
        $ruleRepository->remove($rule);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    private function findOr404(int $id)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        $this->getEventDispatcher()->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        if (null === ($rule = $this->getRuleRepository()->findOneBy([
                'id' => $id,
                'organization' => $tenantContext->getTenant()->getOrganization(),
                'tenantCode' => null,
            ]))) {
            throw new NotFoundHttpException('Organization rule was not found.');
        }

        return $rule;
    }

    private function getRuleRepository()
    {
        return $this->get('swp.repository.rule');
    }

    private function getEventDispatcher()
    {
        return $this->get('event_dispatcher');
    }
}
