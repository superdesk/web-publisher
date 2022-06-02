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

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Matcher\RulesMatcherInterface;
use SWP\Bundle\CoreBundle\Repository\RuleRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Bridge\Events;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OrganizationRuleController extends AbstractController {
  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;
  private CachedTenantContextInterface $cachedTenantContext;
  private EntityManagerInterface $entityManager;
  private RuleRepositoryInterface $ruleRepository;
  private RulesMatcherInterface $rulesMatcher;
  private FactoryInterface $ruleFactory;
  private DataTransformerInterface $dataTransformer;

  /**
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param EntityManagerInterface $entityManager
   * @param RuleRepositoryInterface $ruleRepository
   * @param RulesMatcherInterface $rulesMatcher
   * @param FactoryInterface $ruleFactory
   * @param DataTransformerInterface $dataTransformer
   */
  public function __construct(FormFactoryInterface         $formFactory, EventDispatcherInterface $eventDispatcher,
                              CachedTenantContextInterface $cachedTenantContext, EntityManagerInterface $entityManager,
                              RuleRepositoryInterface      $ruleRepository, RulesMatcherInterface $rulesMatcher,
                              FactoryInterface             $ruleFactory, DataTransformerInterface $dataTransformer) {
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->entityManager = $entityManager;
    $this->ruleRepository = $ruleRepository;
    $this->rulesMatcher = $rulesMatcher;
    $this->ruleFactory = $ruleFactory;
    $this->dataTransformer = $dataTransformer;
  }


  /**
   * @Route("/api/{version}/organization/rules/evaluate", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_organization_rules_evaluate")
   */
  public function rulesEvaluationAction(Request $request): SingleResourceResponseInterface {
    $content = $request->getContent();
    $dispatcher = $this->eventDispatcher;
    $package = $this->dataTransformer->transform($content);
    $dispatcher->dispatch(new GenericEvent($package), Events::SWP_VALIDATION);

    $rules = $this->rulesMatcher->getMatchedRules($package);

    return new SingleResourceResponse($rules);
  }

  /**
   * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_organization_rules")
   */
  public function rulesAction(Request $request): ResourcesListResponseInterface {
    $tenantContext = $this->cachedTenantContext;

    $this->getEventDispatcher()->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

    $repository = $this->getRuleRepository();
    $rules = $repository->getPaginatedByCriteria(
        new Criteria([
            'organization' => $tenantContext->getTenant()->getOrganization(),
            'tenantCode' => null,
        ]),
        $request->query->all('sorting'),
        new PaginationData($request)
    );

    return new ResourcesListResponse($rules);
  }

  /**
   * @Route("/api/{version}/organization/rules/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_organization_rule")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    $ruleRepository = $this->getRuleRepository();

    $rule = $this->ruleFactory->create();
    $form = $this->formFactory->createNamed('', RuleType::class, $rule);
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
   * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_show_organization_rule", requirements={"id"="\d+"})
   */
  public function getAction(int $id): SingleResourceResponseInterface {
    return new SingleResourceResponse($this->findOr404($id));
  }

  /**
   * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_organization_rule", requirements={"id"="\d+"})
   */
  public function updateRuleAction(Request $request, int $id) {
    $objectManager = $this->entityManager;
    $rule = $this->findOr404($id);
    $form = $this->formFactory->createNamed('', RuleType::class, $rule, ['method' => $request->getMethod()]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $objectManager->flush();
      $objectManager->refresh($rule);

      return new SingleResourceResponse($rule);
    }

    return new SingleResourceResponse($form, new ResponseContext(500));
  }

  /**
   * @Route("/api/{version}/organization/rules/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_organization_rule", requirements={"id"="\d+"})
   */
  public function deleteAction(int $id) {
    $rule = $this->findOr404($id);
    $ruleRepository = $this->ruleRepository;
    $ruleRepository->remove($rule);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  private function findOr404(int $id) {
    $tenantContext = $this->cachedTenantContext;
    $this->getEventDispatcher()->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

    if (null === ($rule = $this->getRuleRepository()->findOneBy([
            'id' => $id,
            'organization' => $tenantContext->getTenant()->getOrganization(),
            'tenantCode' => null,
        ]))) {
      throw new NotFoundHttpException('Organization rule was not found.');
    }

    return $rule;
  }

  private function getRuleRepository() {
    return $this->ruleRepository;
  }

  private function getEventDispatcher() {
    return $this->eventDispatcher;
  }
}
