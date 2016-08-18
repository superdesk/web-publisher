<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use FOS\RestBundle\View\View;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Component\Common\Event\HttpCacheEvent;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
     *     }
     * )
     * @Route("/api/{version}/tenants/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_tenants")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $tenants = $this->get('knp_paginator')->paginate(
            $this->getTenantRepository()->findAvailableTenants(),
            $request->get('page', 1),
            $request->get('limit', 10)
        );

        $view = View::create($this->get('swp_pagination_rep')->createRepresentation($tenants, $request), 200);

        return $this->handleView($view);
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
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($code)
    {
        return $this->handleView(View::create($this->findOr404($code), 200));
    }

    /**
     * Deletes a single tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Delete single tenant/website",
     *     statusCodes={
     *         204="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/tenants/{code}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_delete_tenant", requirements={"code"="[a-z0-9]+"})
     * @Method("DELETE")
     */
    public function deleteAction($code)
    {
        $repository = $this->getTenantRepository();
        $tenant = $this->findOr404($code);

        $this->get('event_dispatcher')
            ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($tenant));

        $repository->remove($tenant);

        return $this->handleView(View::create(true, 204));
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
        $form = $this->createForm(new TenantType(), $tenant, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var TenantInterface $formData */
            $formData = $form->getData();

            $this->ensureTenantExists($formData->getSubdomain());

            $formData = $this->assignDefaultOrganization($formData);

            $this->get('swp.repository.tenant')->add($formData);

            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($formData));

            return $this->handleView(View::create($formData, 201));
        }

        return $this->handleView(View::create($form, 400));
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
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $code)
    {
        $tenant = $this->findOr404($code);

        $form = $this->createForm(new TenantType(), $tenant, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var TenantInterface $formData */
            $formData = $form->getData();

            $formData->setUpdatedAt(new \DateTime('now'));
            $this->get('swp.object_manager.tenant')->flush();

            $this->get('event_dispatcher')
                ->dispatch(HttpCacheEvent::EVENT_NAME, new HttpCacheEvent($formData));

            return $this->handleView(View::create($formData, 200));
        }

        return $this->handleView(View::create($form, 400));
    }

    private function findOr404($code)
    {
        if (null === $tenant = $this->getTenantRepository()->findOneByCode($code)) {
            throw $this->createNotFoundException(sprintf('Tenant with code "%s" was not found.', $code));
        }

        return $tenant;
    }

    private function ensureTenantExists($subdomain)
    {
        if (null !== $tenant = $this->getTenantRepository()->findOneBySubdomain($subdomain)) {
            throw new ConflictHttpException(sprintf('Tenant with "%s" subdomain already exists.', $subdomain));
        }

        return $tenant;
    }

    private function assignDefaultOrganization(TenantInterface $tenant)
    {
        if (null === $tenant->getOrganization()) {
            $organization = $this->get('swp.repository.organization')
                ->findOneByName(OrganizationInterface::DEFAULT_NAME);

            if (null === $organization) {
                throw $this->createNotFoundException('Default organization was not found.');
            }

            $tenant->setOrganization($organization);
        }

        return $tenant;
    }

    private function getTenantRepository()
    {
        return $this->get('swp.repository.tenant');
    }
}
