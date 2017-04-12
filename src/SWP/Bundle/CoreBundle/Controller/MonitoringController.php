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
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MonitoringController extends Controller
{
    /**
     * List all items.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all items",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="string", "pattern"="usable|canceled"}
     *     }
     * )
     * @Route("/api/{version}/monitoring/items/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_monitoring_items")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $packages = $this->get('swp.repository.package')
            ->getPaginatedByCriteria(new Criteria([
                'organization' => $tenantContext->getTenant()->getOrganization()->getId(),
                'status' => $request->query->get('status', ''),
            ]), [], new PaginationData($request));

        return new ResourcesListResponse($packages);
    }

    /**
     * Show single item.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single item",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/monitoring/items/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_organization_article", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction(int $id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Publishes article to many websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Publishes article to many tenants",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\MultiplePublishType"
     * )
     * @Route("/api/{version}/monitoring/items/{id}/publish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_publish_monitoring_item", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function publishAction(Request $request, int $id)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');
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
     * Un-publishes article from many websites.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Un-publishes article from many tenants",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\MultipleUnpublishType"
     * )
     * @Route("/api/{version}/monitoring/items/{id}/unpublish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_unpublish_monitoring_item", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function unpublishAction(Request $request, int $id)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');
        $package = $this->findOr404($id);
        $form = $this->createForm(UnpublishFromTenantsType::class, null, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            /** @var Collection $tenants */
            $tenants = $formData['tenants'];
            $this->get('swp_core.article.publisher')->unpublish($package, $tenants->toArray());

            return new SingleResourceResponse(null, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    private function findOr404(int $id)
    {
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        if (null === $package = $this->get('swp.repository.package')->findOneBy([
                'id' => $id,
                'organization' => $tenantContext->getTenant()->getOrganization(),
            ])) {
            throw new NotFoundHttpException('Package was not found.');
        }

        return $package;
    }
}
