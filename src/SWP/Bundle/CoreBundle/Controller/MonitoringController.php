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
use Hoa\Mime\Mime;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType;
use SWP\Bundle\CoreBundle\Form\Type\UnpublishFromTenantsType;
use SWP\Bundle\CoreBundle\Model\ArticleMedia;
use SWP\Bundle\CoreBundle\Model\CompositePublishAction;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Bridge\Events;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MonitoringController extends Controller
{
    /**
     * Receives HTTP Push Request's payload.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Adds a new content from HTTP Push",
     *     statusCodes={
     *         201="Returned on success"
     *     }
     * )
     * @Route("/api/{version}/content/push", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_push")
     * @Method("POST")
     */
    public function pushContentAction(Request $request)
    {
        $content = $request->getContent();
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($content);
        $this->get('event_dispatcher')->dispatch(Events::SWP_VALIDATION, new GenericEvent($package));

        /** @var PackageInterface $existingPackage */
        $existingPackage = $this->findExistingPackage($package);

        if (null !== $existingPackage) {
            $objectManager = $this->get('swp.object_manager.package');
            $package->setId($existingPackage->getId());
            $package->setCreatedAt($existingPackage->getCreatedAt());
            $package->setStatus($existingPackage->getStatus());

            $this->get('event_dispatcher')->dispatch(Events::PACKAGE_PRE_UPDATE, new GenericEvent($existingPackage));

            $objectManager->merge($package);
            $objectManager->flush();

            $this->get('event_dispatcher')->dispatch(Events::PACKAGE_POST_UPDATE, new GenericEvent($package));

            return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
        }

        $this->get('event_dispatcher')->dispatch(Events::PACKAGE_PRE_CREATE, new GenericEvent($package));

        $this->getPackageRepository()->add($package);

        $this->get('event_dispatcher')->dispatch(Events::PACKAGE_POST_CREATE, new GenericEvent($package));

        return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
    }

    /**
     * Receives HTTP Push Request's assets payload which is then processed and stored.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Adds new assets from HTTP Push",
     *     statusCodes={
     *         201="Returned on successful post.",
     *         500="Returned on invalid file.",
     *         200="Returned on form errors"
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\MediaFileType"
     * )
     * @Route("/api/{version}/assets/push", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_assets_push")
     * @Method("POST")
     */
    public function pushAssetsAction(Request $request)
    {
        $form = $this->createForm(MediaFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaManager = $this->get('swp_content_bundle.manager.media');
            $uploadedFile = $form->getData()['media'];
            $mediaId = $request->request->get('media_id');

            if ($uploadedFile->isValid()) {
                $image = $this->get('swp.repository.image')->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));

                if (null == $image) {
                    $image = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);

                    $this->get('swp.object_manager.media')->flush();
                }

                return new SingleResourceResponse([
                    'media_id' => $mediaId,
                    'URL' => $mediaManager->getMediaPublicUrl($image),
                    'media' => base64_encode($mediaManager->getFile($image)),
                    'mime_type' => Mime::getMimeFromExtension($image->getFileExtension()),
                    'filemeta' => [],
                ], new ResponseContext(201));
            }

            throw new \Exception('Uploaded file is not valid:'.$uploadedFile->getErrorMessage());
        }

        return new SingleResourceResponse($form);
    }

    /**
     * Checks if media exists in storage.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Gets a single media file",
     *     statusCodes={
     *         404="Returned when file doesn't exist.",
     *         200="Returned on form errors"
     *     }
     * )
     * @Route("/api/{version}/assets/push/{mediaId}", options={"expose"=true}, defaults={"version"="v1"}, requirements={"mediaId"=".+"}, name="swp_api_assets_get")
     * @Route("/api/{version}/assets/get/{mediaId}", options={"expose"=true}, defaults={"version"="v1"}, requirements={"mediaId"=".+"}, name="swp_api_assets_get_1")
     * @Method("GET")
     */
    public function getAssetsAction($mediaId)
    {
        $image = $this->get('swp.repository.image')
            ->findImageByAssetId(ArticleMedia::handleMediaId($mediaId));

        if (null === $image) {
            throw new NotFoundHttpException('Media don\'t exist in storage');
        }

        $mediaManager = $this->get('swp_content_bundle.manager.media');

        return new SingleResourceResponse([
            'media_id' => $mediaId,
            'URL' => $mediaManager->getMediaPublicUrl($image),
            'media' => base64_encode($mediaManager->getFile($image)),
            'mime_type' => Mime::getMimeFromExtension($image->getFileExtension()),
            'filemeta' => [],
        ]);
    }

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
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $packages = $this->getPackageRepository()
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
     * @Route("/api/{version}/monitoring/items/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_show_organization_package", requirements={"id"="\d+"})
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
     *     input="SWP\Bundle\CoreBundle\Form\Type\CompositePublishActionType"
     * )
     * @Route("/api/{version}/monitoring/items/{id}/publish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_publish_monitoring_item", requirements={"id"="\d+"})
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
     *     input="SWP\Bundle\CoreBundle\Form\Type\UnpublishFromTenantsType"
     * )
     * @Route("/api/{version}/monitoring/items/{id}/unpublish/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_unpublish_monitoring_item", requirements={"id"="\d+"})
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

            return new SingleResourceResponse(null, new ResponseContext(201));
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

    protected function findExistingPackage(PackageInterface $package)
    {
        $existingPackage = $this->getPackageRepository()->findOneBy(['guid' => $package->getGuid()]);

        if (null === $existingPackage) {
            $existingPackage = $this->getPackageRepository()->findOneBy([
                'evolvedFrom' => $package->getEvolvedFrom(),
            ]);
        }

        return $existingPackage;
    }

    protected function getPackageRepository()
    {
        return $this->get('swp.repository.package');
    }
}
