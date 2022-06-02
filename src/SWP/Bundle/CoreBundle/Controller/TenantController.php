<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
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

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Factory\TenantFactoryInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use function array_key_exists;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController as FOSRestController;
use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TenantController extends FOSRestController {

  private CachedTenantContextInterface $cachedTenantContext;
  private EventDispatcherInterface $eventDispatcher;
  private FormFactoryInterface $formFactory;
  private TenantRepositoryInterface $tenantRepository;
  private EntityManagerInterface $entityManager;
  private SettingsManagerInterface $settingsManager;
  private TenantFactoryInterface $tenantFactory;
  private ArticleRepositoryInterface $articleRepository;

  /**
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param EventDispatcherInterface $eventDispatcher
   * @param FormFactoryInterface $formFactory
   * @param TenantRepositoryInterface $tenantRepository
   * @param EntityManagerInterface $entityManager
   * @param SettingsManagerInterface $settingsManager
   * @param TenantFactoryInterface $tenantFactory
   * @param ArticleRepositoryInterface $articleRepository
   */
  public function __construct(CachedTenantContextInterface $cachedTenantContext,
                              EventDispatcherInterface     $eventDispatcher, FormFactoryInterface $formFactory,
                              TenantRepositoryInterface    $tenantRepository, EntityManagerInterface $entityManager,
                              SettingsManagerInterface     $settingsManager, TenantFactoryInterface $tenantFactory,
                              ArticleRepositoryInterface   $articleRepository) {
    $this->cachedTenantContext = $cachedTenantContext;
    $this->eventDispatcher = $eventDispatcher;
    $this->formFactory = $formFactory;
    $this->tenantRepository = $tenantRepository;
    $this->entityManager = $entityManager;
    $this->settingsManager = $settingsManager;
    $this->tenantFactory = $tenantFactory;
    $this->articleRepository = $articleRepository;
  }


  /**
   * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_tenants")
   */
  public function listAction(Request $request) {
    $tenants = $this->getTenantRepository()
        ->getPaginatedByCriteria($this->eventDispatcher, new Criteria(), $request->query->all('sorting'), new PaginationData($request));
    $responseContext = new ResponseContext();
    $responseContext->setSerializationGroups(['Default', 'api', 'details_api']);

    return new ResourcesListResponse($tenants, $responseContext);
  }

  /**
   * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_tenant", requirements={"code"="[a-z0-9]+"})
   */
  public function getAction($code) {
    return new SingleResourceResponse($this->findOr404($code));
  }

  /**
   * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_tenant", requirements={"code"="[a-z0-9]+"})
   */
  public function deleteAction(Request $request, $code) {
    $tenantContext = $this->cachedTenantContext;
    $eventDispatcher = $this->eventDispatcher;
    $currentTenant = $tenantContext->getTenant();

    $repository = $this->getTenantRepository();
    $tenant = $this->findOr404($code);

    $forceRemove = $request->query->has('force');
    if (!$forceRemove) {
      $tenantContext->setTenant($tenant);
      $eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);
      $articlesRepository = $this->articleRepository;
      $existingArticles = $articlesRepository->findAll();
      if (0 !== \count($existingArticles)) {
        throw new ConflictHttpException('This tenant have articles attached to it.');
      }
    }

    $repository->remove($tenant);

    $tenantContext->setTenant($currentTenant);
    $eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_ENABLE);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  /**
   * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_tenant")
   */
  public function createAction(Request $request) {
    $tenant = $this->tenantFactory->create();
    $tenantContext = $this->cachedTenantContext;
    $tenantObjectManager = $this->entityManager;
    $form = $this->formFactory->createNamed('', TenantType::class, $tenant, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->ensureTenantDontExists($tenant->getDomainName(), $tenant->getSubdomain());
      if (null === $tenant->getOrganization()) {
        $organization = $tenantObjectManager->merge($tenantContext->getTenant()->getOrganization());
        $tenant->setOrganization($organization);
      }
      $this->getTenantRepository()->add($tenant);

      return new SingleResourceResponse($tenant, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_tenant", requirements={"code"="[a-z0-9]+"})
   */
  public function updateAction(Request $request, $code) {
    $tenant = $this->findOr404($code);
    $form = $this->formFactory->createNamed('', TenantType::class, $tenant, ['method' => $request->getMethod()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $formData = $request->request->all();
      $tenant->setUpdatedAt(new DateTime('now'));
      $this->entityManager->flush();

      $tenantContext = $this->cachedTenantContext;
      $tenantContext->setTenant($tenant);

      $settingsManager = $this->settingsManager;

      if (array_key_exists('fbiaEnabled', $formData)) {
        $settingsManager->set('fbia_enabled', (bool)$formData['fbiaEnabled'], ScopeContextInterface::SCOPE_TENANT, $tenant);
      }
      if (array_key_exists('paywallEnabled', $formData)) {
        $settingsManager->set('paywall_enabled', (bool)$formData['paywallEnabled'], ScopeContextInterface::SCOPE_TENANT, $tenant);
      }
      if (array_key_exists('defaultLanguage', $formData)) {
        $settingsManager->set('default_language', $formData['defaultLanguage'], ScopeContextInterface::SCOPE_TENANT, $tenant);
      }

      return new SingleResourceResponse($tenant);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @param string $code
   *
   * @return mixed|TenantInterface|null
   * @throws NotFoundHttpException
   *
   */
  private function findOr404($code) {
    if (null === $tenant = $this->getTenantRepository()->findOneByCode($code)) {
      throw $this->createNotFoundException(sprintf('Tenant with code "%s" was not found.', $code));
    }

    return $tenant;
  }

  /**
   * @return mixed|TenantInterface|null
   */
  private function ensureTenantDontExists(string $domain, string $subdomain = null) {
    if (null !== $tenant = $this->getTenantRepository()->findOneBySubdomainAndDomain($subdomain, $domain)) {
      throw new ConflictHttpException('Tenant for this host already exists.');
    }

    return $tenant;
  }

  /**
   * @return object|\SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\TenantRepository
   */
  private function getTenantRepository() {
    return $this->tenantRepository;
  }
}
