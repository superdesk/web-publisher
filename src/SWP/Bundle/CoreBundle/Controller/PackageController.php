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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType;
use SWP\Bundle\CoreBundle\Form\Type\PackageType;
use SWP\Bundle\CoreBundle\Form\Type\UnpublishFromTenantsType;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Model\ContentInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PackageController extends Controller
{
    /**
     * List all items.
     *
     * @Operation(
     *     tags={"package"},
     *     summary="List all packages",
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="options: new|published|unpublished|canceled",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [updatedAt]=asc|desc",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Package::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/packages/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_packages")
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
     * @Operation(
     *     tags={"package"},
     *     summary="Show single package",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Package::class, groups={"api"})
     *     )
     * )
     *
     * @Route("/api/{version}/packages/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_show_package", requirements={"id"="\d+"})
     */
    public function getAction(int $id): SingleResourceResponseInterface
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    /**
     * Publishes package to many websites.
     *
     * @Operation(
     *     tags={"package"},
     *     summary="Publishes package to many tenants",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=CompositePublishActionType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when validation failed."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/packages/{id}/publish/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_publish_package", requirements={"id"="\d+"})
     */
    public function publishAction(Request $request, int $id): SingleResourceResponseInterface
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        /** @var PackageInterface $package */
        $package = $this->findOr404($id);

        $form = $this->get('form.factory')->createNamed('', CompositePublishActionType::class, new CompositePublishAction(), ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('swp_core.article.publisher')->publish($package, $form->getData());
            $this->get('fos_elastica.object_persister.swp_package.package')->replaceOne($package);

            return new SingleResourceResponse(null, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Un-publishes package from many websites.
     *
     * @Operation(
     *     tags={"package"},
     *     summary="Un-publishes package from many tenants",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=UnpublishFromTenantsType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when validation failed."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/packages/{id}/unpublish/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_unpublish_package", requirements={"id"="\d+"})
     */
    public function unpublishAction(Request $request, int $id): SingleResourceResponseInterface
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $package = $this->findOr404($id);
        $form = $this->get('form.factory')->createNamed('', UnpublishFromTenantsType::class, null, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            /** @var Collection $tenants */
            $tenants = $formData['tenants'];
            $this->get('swp_core.article.publisher')->unpublish($package, $tenants->toArray());

            return new SingleResourceResponse(null, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Update package.
     *
     * @Operation(
     *     tags={"package"},
     *     summary="Updates package",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=PackageType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when validation failed."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/packages/{id}/", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_package", requirements={"id"="\d+"})
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request, int $id)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $package = $this->findOr404($id);
        $form = $this->get('form.factory')->createNamed('', PackageType::class, $package, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (ContentInterface::STATUS_CANCELED === $package->getPubStatus()) {
                $package->setStatus(ContentInterface::STATUS_CANCELED);
            }
            $this->getPackageRepository()->flush();

            return new SingleResourceResponse($package, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @return object|null
     */
    private function findOr404(int $id)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        if (null === $package = $this->getPackageRepository()->findOneBy(['id' => $id, 'organization' => $tenantContext->getTenant()->getOrganization()])) {
            throw new NotFoundHttpException('Package was not found.');
        }

        return $package;
    }

    protected function getPackageRepository()
    {
        return $this->get('swp.repository.package');
    }
}
