<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use Hoa\Mime\Mime;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Component\Bridge\Events;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentPushController extends Controller
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
            $this->get('event_dispatcher')->dispatch(Events::PACKAGE_PRE_UPDATE, new GenericEvent($package));
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

    protected function findExistingPackage(PackageInterface $package)
    {
        $existingPackage = $this->getPackageRepository()->findOneBy(['guid' => $package->getGuid()]);

        if (null === $existingPackage) {
            $existingPackage = $this->getPackageRepository()->findOneBy([
                'guid' => $package->getEvolvedFrom(),
            ]);
        }

        return $existingPackage;
    }

    protected function getPackageRepository()
    {
        return $this->get('swp.repository.package');
    }
}
