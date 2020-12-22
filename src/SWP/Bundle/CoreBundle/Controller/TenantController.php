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

use function array_key_exists;
use DateTime;
use FOS\RestBundle\Controller\FOSRestController;
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

class TenantController extends FOSRestController
{
    /**
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_tenants")
     */
    public function listAction(Request $request)
    {
        $tenants = $this->getTenantRepository()
            ->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));
        $responseContext = new ResponseContext();
        $responseContext->setSerializationGroups(['Default', 'api', 'details_api']);

        return new ResourcesListResponse($tenants, $responseContext);
    }

    /**
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_tenant", requirements={"code"="[a-z0-9]+"})
     */
    public function getAction($code)
    {
        return new SingleResourceResponse($this->findOr404($code));
    }

    /**
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_tenant", requirements={"code"="[a-z0-9]+"})
     */
    public function deleteAction(Request $request, $code)
    {
        $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');
        $eventDispatcher = $this->container->get('event_dispatcher');
        $currentTenant = $tenantContext->getTenant();

        $repository = $this->getTenantRepository();
        $tenant = $this->findOr404($code);

        $forceRemove = $request->query->has('force');
        if (!$forceRemove) {
            $tenantContext->setTenant($tenant);
            $eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
            $articlesRepository = $this->get('swp.repository.article');
            $existingArticles = $articlesRepository->findAll();
            if (0 !== \count($existingArticles)) {
                throw new ConflictHttpException('This tenant have articles attached to it.');
            }
        }

        $repository->remove($tenant);

        $tenantContext->setTenant($currentTenant);
        $eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_tenant")
     */
    public function createAction(Request $request)
    {
        $tenant = $this->get('swp.factory.tenant')->create();
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        $tenantObjectManager = $this->get('swp.object_manager.tenant');
        $form = $this->get('form.factory')->createNamed('', TenantType::class, $tenant, ['method' => $request->getMethod()]);
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
    public function updateAction(Request $request, $code)
    {
        $tenant = $this->findOr404($code);
        $form = $this->get('form.factory')->createNamed('', TenantType::class, $tenant, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all();
            $tenant->setUpdatedAt(new DateTime('now'));
            $this->get('swp.object_manager.tenant')->flush();

            $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
            $tenantContext->setTenant($tenant);

            $settingsManager = $this->get('swp_settings.manager.settings');

            if (array_key_exists('fbiaEnabled', $formData)) {
                $settingsManager->set('fbia_enabled', (bool) $formData['fbiaEnabled'], ScopeContextInterface::SCOPE_TENANT, $tenant);
            }
            if (array_key_exists('paywallEnabled', $formData)) {
                $settingsManager->set('paywall_enabled', (bool) $formData['paywallEnabled'], ScopeContextInterface::SCOPE_TENANT, $tenant);
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
     * @throws NotFoundHttpException
     *
     * @return mixed|TenantInterface|null
     */
    private function findOr404($code)
    {
        if (null === $tenant = $this->getTenantRepository()->findOneByCode($code)) {
            throw $this->createNotFoundException(sprintf('Tenant with code "%s" was not found.', $code));
        }

        return $tenant;
    }

    /**
     * @return mixed|TenantInterface|null
     */
    private function ensureTenantDontExists(string $domain, string $subdomain = null)
    {
        if (null !== $tenant = $this->getTenantRepository()->findOneBySubdomainAndDomain($subdomain, $domain)) {
            throw new ConflictHttpException('Tenant for this host already exists.');
        }

        return $tenant;
    }

    /**
     * @return object|\SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\TenantRepository
     */
    private function getTenantRepository()
    {
        return $this->get('swp.repository.tenant');
    }
}
