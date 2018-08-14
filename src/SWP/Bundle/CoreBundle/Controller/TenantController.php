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

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Context\CachedTenantContext;
use SWP\Bundle\CoreBundle\Model\RevisionInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantController extends FOSRestController
{
    /**
     * List all tenants/websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all tenants/websites",
     *     statusCodes={
     *         200="Returned on success.",
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_tenants")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $tenants = $this->getTenantRepository()
            ->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($tenants);
    }

    /**
     * Shows a single tenant/website.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single tenant/website",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_get_tenant", requirements={"code"="[a-z0-9]+"})
     * @Method("GET")
     */
    public function getAction($code)
    {
        return new SingleResourceResponse($this->findOr404($code));
    }

    /**
     * Deletes a single tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single tenant/website",
     *     statusCodes={
     *         204="Returned on success."
     *     },
     *     parameters={
     *         {"name"="force", "dataType"="bool", "required"=false, "description"="Remove tenant ignoring attached articles"}
     *     }
     * )
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_tenant", requirements={"code"="[a-z0-9]+"})
     * @Method("DELETE")
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
     * Creates a new tenant/website.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create new tenant/website",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned on failure.",
     *         409="Returned on conflict."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\TenantType"
     * )
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_create_tenant")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $tenant = $this->get('swp.factory.tenant')->create();
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');
        $tenantObjectManager = $this->get('swp.object_manager.tenant');
        $form = $this->createForm(TenantType::class, $tenant, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->ensureTenantDontExists($tenant->getDomainName(), $tenant->getSubdomain());
            if (null === $tenant->getOrganization()) {
                $organization = $tenantObjectManager->merge($tenantContext->getTenant()->getOrganization());
                $tenant->setOrganization($organization);
            }
            $this->getTenantRepository()->add($tenant);

            /** @var RevisionManagerInterface $revisionManager */
            $revisionManager = $this->get('swp.manager.revision');
            /** @var RevisionInterface $revision */
            $revision = $revisionManager->create();
            $revision->setTenantCode($tenant->getCode());
            $revisionManager->publish($revision);

            return new SingleResourceResponse($tenant, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Updates a single tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update single tenant",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned on failure.",
     *         404="Returned when not found.",
     *         409="Returned on conflict."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\TenantType"
     * )
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_update_tenant", requirements={"code"="[a-z0-9]+"})
     *
     * @Method("PATCH")
     *
     * @param Request $request
     * @param string  $code
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request, $code)
    {
        $tenant = $this->findOr404($code);
        $form = $this->createForm(TenantType::class, $tenant, ['method' => $request->getMethod()]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $tenant->setUpdatedAt(new \DateTime('now'));
            $this->get('swp.object_manager.tenant')->flush();

            $cacheProvider = $this->get('doctrine_cache.providers.main_cache');
            $cacheProvider->save(CachedTenantContext::getCacheKey($request->getHost()), $tenant);

            return new SingleResourceResponse($tenant);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @param string $code
     *
     * @throws NotFoundHttpException
     *
     * @return mixed|null|TenantInterface
     */
    private function findOr404($code)
    {
        if (null === $tenant = $this->getTenantRepository()->findOneByCode($code)) {
            throw $this->createNotFoundException(sprintf('Tenant with code "%s" was not found.', $code));
        }

        return $tenant;
    }

    /**
     * @param string      $domain
     * @param string|null $subdomain
     *
     * @return mixed|null|TenantInterface
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
