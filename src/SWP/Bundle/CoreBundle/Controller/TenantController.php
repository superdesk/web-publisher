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
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TenantController extends FOSRestController
{
    /**
     * List all tenants/websites.
     *
     * @Operation(
     *     tags={""},
     *     summary="List all tenants/websites",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_tenants")
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
     * @Operation(
     *     tags={""},
     *     summary="Show single tenant/website",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_tenant", requirements={"code"="[a-z0-9]+"})
     */
    public function getAction($code)
    {
        return new SingleResourceResponse($this->findOr404($code));
    }

    /**
     * Deletes a single tenant.
     *
     * @Operation(
     *     tags={""},
     *     summary="Delete single tenant/website",
     *     @SWG\Parameter(
     *         name="force",
     *         in="body",
     *         description="Remove tenant ignoring attached articles",
     *         required=false,
     *         @SWG\Schema(type="bool")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     )
     * )
     *
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
     * Creates a new tenant/website.
     *
     * @Operation(
     *     tags={""},
     *     summary="Create new tenant/website",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Tenant name",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="subdomain",
     *         in="body",
     *         description="Tenant subdomain",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="domainName",
     *         in="body",
     *         description="Tenant domain name",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="themeName",
     *         in="body",
     *         description="Tenant theme name",
     *         required=false,
     *         @SWG\Schema(type="choice")
     *     ),
     *     @SWG\Parameter(
     *         name="organization",
     *         in="body",
     *         description="Tenant organization code",
     *         required=false,
     *         @SWG\Schema(type="choice")
     *     ),
     *     @SWG\Parameter(
     *         name="ampEnabled",
     *         in="body",
     *         description="Defines whether Google AMP HTML support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="fbiaEnabled",
     *         in="body",
     *         description="Defines whether Facebook Instant Articles support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="paywallEnabled",
     *         in="body",
     *         description="Defines whether Paywall support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="outputChannel",
     *         in="body",
     *         description="Output Channel",
     *         required=false,
     *         @SWG\Schema(type="object (OutputChannelType)")
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned on failure."
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Returned on conflict."
     *     )
     * )
     *
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
     * Updates a single tenant.
     *
     * @Operation(
     *     tags={""},
     *     summary="Update single tenant",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Tenant name",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="subdomain",
     *         in="body",
     *         description="Tenant subdomain",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="domainName",
     *         in="body",
     *         description="Tenant domain name",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="themeName",
     *         in="body",
     *         description="Tenant theme name",
     *         required=false,
     *         @SWG\Schema(type="choice")
     *     ),
     *     @SWG\Parameter(
     *         name="organization",
     *         in="body",
     *         description="Tenant organization code",
     *         required=false,
     *         @SWG\Schema(type="choice")
     *     ),
     *     @SWG\Parameter(
     *         name="ampEnabled",
     *         in="body",
     *         description="Defines whether Google AMP HTML support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="fbiaEnabled",
     *         in="body",
     *         description="Defines whether Facebook Instant Articles support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="paywallEnabled",
     *         in="body",
     *         description="Defines whether Paywall support is enabled or not (true or false).",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="outputChannel",
     *         in="body",
     *         description="Output Channel",
     *         required=false,
     *         @SWG\Schema(type="object (OutputChannelType)")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned on failure."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found."
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Returned on conflict."
     *     )
     * )
     *
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_tenant", requirements={"code"="[a-z0-9]+"})
     *
     * @param Request $request
     * @param string  $code
     *
     * @return SingleResourceResponse
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
     * @param string      $domain
     * @param string|null $subdomain
     *
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
