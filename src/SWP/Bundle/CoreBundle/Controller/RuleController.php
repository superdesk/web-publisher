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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Operation(
     *     tags={"tenant rule"},
     *     summary="List all Rule entities for current tenant.",
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
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_rule")
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
     * @Operation(
     *     tags={"tenant rule"},
     *     summary="Get single rule",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Rule::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/rules/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_rule")
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function getAction(RuleInterface $rule)
    {
        return new SingleResourceResponse($rule);
    }

    /**
     * @Operation(
     *     tags={"tenant rule"},
     *     summary="Create new rule",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
     *         required=true,
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
     * @Route("/api/{version}/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_rule")
     */
    public function createAction(Request $request)
    {
        $ruleRepository = $this->get('swp.repository.rule');

        $rule = $this->get('swp.factory.rule')->create();
        $form = $form = $this->get('form.factory')->createNamed('', RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ruleRepository->add($rule);

            return new SingleResourceResponse($rule, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Operation(
     *     tags={"tenant rule"},
     *     summary="Delete single rule",
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
     * @Route("/api/{version}/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_delete_rule", methods={"DELETE"}, requirements={"id"="\d+"})
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function deleteAction(RuleInterface $rule)
    {
        $ruleRepository = $this->get('swp.repository.rule');
        $ruleRepository->remove($rule);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Operation(
     *     tags={"tenant rule"},
     *     summary="Update single rule",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
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
     *         response="404",
     *         description="Rule not found."
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="Method Not Allowed."
     *     )
     * )
     *
     * @Route("/api/{version}/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_rule", requirements={"id"="\d+"})
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function updateAction(Request $request, RuleInterface $rule)
    {
        $objectManager = $this->get('swp.object_manager.rule');

        $form = $form = $this->get('form.factory')->createNamed('', RuleType::class, $rule, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($rule);

            return new SingleResourceResponse($rule);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
