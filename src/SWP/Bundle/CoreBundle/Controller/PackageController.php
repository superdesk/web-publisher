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

use Doctrine\Common\Collections\Collection;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType;
use SWP\Bundle\CoreBundle\Form\Type\UnpublishFromTenantsType;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PackageController extends Controller
{
    /**
     * List all items.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all packages",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="string", "pattern"="published|unpublished|new|canceled"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/packages/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_packages")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $packages = $this->getPackageRepository()
            ->getPaginatedByCriteria(new Criteria([
                'organization' => $tenantContext->getTenant()->getOrganization()->getId(),
                'status' => $request->query->get('status', ''),
            ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($packages);
    }

    /**
     * Show single package.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single package",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/packages/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_package", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(int $id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Publishes package to many websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Publishes package to many tenants",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType"
     * )
     * @Route("/api/{version}/packages/{id}/publish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_publish_package", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function publishAction(Request $request, int $id)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $package = $this->findOr404($id);

        $form = $this->createForm(CompositePublishActionType::class, new CompositePublishAction(), ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('swp_core.article.publisher')->publish($package, $form->getData());

            return new SingleResourceResponse(null, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * Un-publishes package from many websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Un-publishes package from many tenants",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\UnpublishFromTenantsType"
     * )
     * @Route("/api/{version}/packages/{id}/unpublish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_unpublish_package", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function unpublishAction(Request $request, int $id)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $package = $this->findOr404($id);
        $form = $this->createForm(UnpublishFromTenantsType::class, null, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            /** @var Collection $tenants */
            $tenants = $formData['tenants'];
            $this->get('swp_core.article.publisher')->unpublish($package, $tenants->toArray());

            return new SingleResourceResponse(null, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    private function findOr404(int $id)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        if (null === $package = $this->getPackageRepository()->findOneBy([
                'id' => $id,
                'organization' => $tenantContext->getTenant()->getOrganization(),
            ])) {
            throw new NotFoundHttpException('Package was not found.');
        }

        return $package;
    }

    protected function getPackageRepository()
    {
        return $this->get('swp.repository.package');
    }
}
