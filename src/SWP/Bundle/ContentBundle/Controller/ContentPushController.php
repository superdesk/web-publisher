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
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Provider\FileProvider;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMessage;
use SWP\Component\Bridge\Events;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContentPushController extends AbstractController
{
    /**
     * @Route("/api/{version}/content/push", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_push")
     */
    public function pushContentAction(Request $request): SingleResourceResponseInterface
    {
        $package = $this->container->get('swp_bridge.transformer.json_to_package')->transform($request->getContent());
        $this->container->get('event_dispatcher')->dispatch(new GenericEvent($package), Events::SWP_VALIDATION);

        $currentTenant = $this->container->get('swp_multi_tenancy.tenant_context')->getTenant();

        $this->dispatchMessage(new ContentPushMessage($currentTenant->getId(), $request->getContent()));

        return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
    }

    /**
     * @Route("/api/{version}/assets/push", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_assets_push")
     */
    public function pushAssetsAction(Request $request): SingleResourceResponseInterface
    {
        $form = $this->get('form.factory')->createNamed('', MediaFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaManager = $this->get('swp_content_bundle.manager.media');
            $uploadedFile = $form->getData()['media'];
            $mediaId = $request->request->get('mediaId');

            if ($uploadedFile->isValid()) {
                $fileProvider = $this->container->get(FileProvider::class);
                $file = $fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $uploadedFile->guessExtension());

                if (null === $file) {
                    $file = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);
                    $this->get('swp.object_manager.media')->flush();
                }

                return new SingleResourceResponse(
                    [
                        'media_id' => $mediaId,
                        'URL' => $mediaManager->getMediaPublicUrl($file),
                        'media' => base64_encode($mediaManager->getFile($file)),
                        'mime_type' => Mime::getMimeFromExtension($file->getFileExtension()),
                        'filemeta' => [],
                    ],
                    new ResponseContext(201)
                );
            }

            throw new \Exception('Uploaded file is not valid:'.$uploadedFile->getErrorMessage());
        }

        return new SingleResourceResponse($form);
    }

    /**
     * @Route("/api/{version}/assets/{action}/{mediaId}.{extension}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, requirements={"mediaId"=".+", "action"="get|push"}, name="swp_api_assets_get")
     */
    public function getAssetsAction(string $mediaId, string $extension): SingleResourceResponseInterface
    {
        $fileProvider = $this->container->get(FileProvider::class);
        $file = $fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $extension);

        if (null === $file) {
            throw new NotFoundHttpException('Media don\'t exist in storage');
        }

        $mediaManager = $this->get('swp_content_bundle.manager.media');

        return new SingleResourceResponse([
            'media_id' => $mediaId,
            'URL' => $mediaManager->getMediaPublicUrl($file),
            'media' => base64_encode($mediaManager->getFile($file)),
            'mime_type' => Mime::getMimeFromExtension($file->getFileExtension()),
            'filemeta' => [],
        ]);
    }

    protected function getPackageRepository()
    {
        return $this->get('swp.repository.package');
    }
}
