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

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\CoreBundle\Matcher\RulesMatcher;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Bridge\Events;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrganizationRuleController extends AbstractController
{
    /**
     * @Operation(
     *     tags={"organization rule"},
     *     summary="Returns a list of rules that will be executed on the package",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Package::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success"
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/evaluate", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_organization_rules_evaluate")
     */
    public function rulesEvaluationAction(Request $request): SingleResourceResponseInterface
    {
        $content = $request->getContent();
        $dispatcher = $this->get('event_dispatcher');
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($content);
        $dispatcher->dispatch(Events::SWP_VALIDATION, new GenericEvent($package));

        $rules = $this->get(RulesMatcher::class)->getMatchedRules($package);

        return new SingleResourceResponse($rules);
    }

    /**
     * List all current organization's rules.
     *
     * @Operation(
     *     tags={"organization rule"},
     *     summary="List all current organization's articles",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [updatedAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Rule::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error occurred."
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_organization_rules")
     */
    public function rulesAction(Request $request): ResourcesListResponseInterface
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
     * @Operation(
     *     tags={"organization rule"},
     *     summary="Create a new organization rule",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=RuleType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Rule::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned on validation error."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_organization_rule")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        $ruleRepository = $this->getRuleRepository();

        $rule = $this->get('swp.factory.rule')->create();
        $form = $form = $this->get('form.factory')->createNamed('', RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Operation(
     *     tags={"organization rule"},
     *     summary="Show single organization rule",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Rule::class, groups={"api"})
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_show_organization_rule", requirements={"id"="\d+"})
     */
    public function getAction(int $id): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Updates organization's rule.
     *
     * @Operation(
     *     tags={"organization rule"},
     *     summary="Updates organization rule",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=RuleType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Rule::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when validation failed."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_organization_rule", requirements={"id"="\d+"})
     */
    public function updateRuleAction(Request $request, int $id)
    {
        $objectManager = $this->get('swp.object_manager.rule');
        $rule = $this->findOr404($id);
        $form = $form = $this->get('form.factory')->createNamed('', RuleType::class, $rule, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($rule);

            return new SingleResourceResponse($rule);
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * Delete single organization rule.
     *
     * @Operation(
     *     tags={"organization rule"},
     *     summary="Delete single organization rule",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Returned when method not allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_organization_rule", requirements={"id"="\d+"})
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
