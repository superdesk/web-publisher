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
     * @Route("/api/{version}/content/push/{mediaId}", options={"expose"=true}, defaults={"version"="v1"}, requirements={"mediaId"=".+"}, name="swp_api_content_push")
     * @Method("POST")
     */
    public function pushContentAction(Request $request, $mediaId = null)
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
     *     description="Adds a new assets from HTTP Push",
     *     statusCodes={
     *         201="Returned on successful post."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\MediaFileType"
     * )
     * @Route("/api/{version}/assets/push/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_assets_push")
     * @Method("POST")
     */
    public function pushAssetsAction(Request $request)
    {
        $form = $this->createForm(new MediaFileType(), null, ['method' => $request->getMethod()]);

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
                    'media' => base64_encode($mediaManager->getFile($media)), //base64 string encoded media file; not returned by default
                    'mime_type' =>  Mime::getMimeFromExtension($media->getFileExtension()), //the mime type of the media file
                    'filemeta' => [], //the metadata of the media file (this field may be missing if the content API was unable to retrieve the metadata from the file)
                ], 201));
            }

            return $this->handleView(View::create(['status' => 'Uploaded file is invalid'], 500));
        }

        return $this->handleView(View::create($form, 200));
    }


}
