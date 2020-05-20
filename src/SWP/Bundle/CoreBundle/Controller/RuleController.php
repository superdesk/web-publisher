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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Rule\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RuleController extends FOSRestController
{
    /**
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
     * @Route("/api/{version}/rules/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_rule")
     * @ParamConverter("rule", class="SWP\Bundle\CoreBundle\Model\Rule")
     */
    public function getAction(RuleInterface $rule)
    {
        return new SingleResourceResponse($rule);
    }

    /**
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
