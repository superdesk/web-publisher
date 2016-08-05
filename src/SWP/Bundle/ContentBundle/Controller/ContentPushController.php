<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use Hoa\Mime\Mime;
use League\Pipeline\Pipeline;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ContentPushController extends FOSRestController
{
    /**
     * Receives HTTP Push Request's payload which is then processed by the pipeline.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Adds a new content from HTTP Push",
     *     statusCodes={
     *         201="Returned on successful post."
     *     }
     * )
     * @Route("/api/{version}/content/push", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_push")
     * @Method("POST")
     */
    public function pushContentAction(Request $request)
    {
        $pipeline = (new Pipeline())
            ->pipe([$this->get('swp_bridge.transformer.json_to_package'), 'transform'])
            ->pipe(function ($package) {
                $this->get('swp.repository.package')->add($package);

                return $package;
            })
            // TODO create content component and include it into bridge bundle
            ->pipe([$this->get('swp_content.transformer.package_to_article'), 'transform'])
            ->pipe(function ($article) {
                $this->get('swp.repository.article')->add($article);
                $this->get('event_dispatcher')->dispatch(ArticleEvents::POST_CREATE, new ArticleEvent($article));

                return $article;
            });

        $pipeline->process($request->getContent());

        return $this->handleView(View::create(['status' => 'OK'], 201));
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
        $form = $this->createForm(new MediaFileType());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->getData()['media'];
            $mediaId = $request->request->get('media_id');
            if ($uploadedFile->isValid()) {
                $mediaManager = $this->container->get('swp_content_bundle.manager.media');
                $media = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);

                return $this->handleView(View::create([
                    'media_id' => $mediaId,
                    'URL' => $mediaManager->getMediaPublicUrl($media),
                    'media' => base64_encode($mediaManager->getFile($media)),
                    'mime_type' => Mime::getMimeFromExtension($media->getFileExtension()),
                    'filemeta' => [],
                ], 201));
            }

            throw new \Exception('Uploaded file is not valid:'.$uploadedFile->getError());
        }

        return $this->handleView(View::create($form, 200));
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
    public function getAssetsAction(Request $request, $mediaId)
    {
        $objectManager = $this->container->get('swp.object_manager.media');
        $pathBuilder = $this->container->get('swp_multi_tenancy.path_builder');
        $mediaBasepath = $this->container->getParameter('swp_multi_tenancy.persistence.phpcr.media_basepath');

        $media = $objectManager->find(null, $pathBuilder->build($mediaBasepath).'/'.$mediaId);

        if (null === $media) {
            throw new ResourceNotFoundException('Media don\'t exists in storage');
        }

        $mediaManager = $this->container->get('swp_content_bundle.manager.media');

        return $this->handleView(View::create([
            'media_id' => $mediaId,
            'URL' => $mediaManager->getMediaPublicUrl($media),
            'media' => base64_encode($mediaManager->getFile($media)),
            'mime_type' => Mime::getMimeFromExtension($media->getFileExtension()),
            'filemeta' => [],
        ], 200));
    }
}
