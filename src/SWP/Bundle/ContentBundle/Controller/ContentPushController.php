<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use Behat\Transliterator\Transliterator;
use Hoa\Mime\Mime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentPushController extends Controller
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
        $package = $this->handlePackage($request);
        $articleRepository = $this->get('swp.repository.article');

        $existingArticle = $articleRepository->findOneBy(['slug' => Transliterator::urlize($package->getSlugline())]);

        if (null !== $existingArticle) {
            $this->get('swp.hydrator.article')->hydrate($existingArticle, $package);
            $this->get('swp.object_manager.article')->flush();

            return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
        }

        $article = $this->get('swp_content.transformer.package_to_article')->transform($package);
        $this->get('event_dispatcher')->dispatch(ArticleEvents::PRE_CREATE, new ArticleEvent($article, $package));
        $articleRepository->add($article);
        $this->get('event_dispatcher')->dispatch(ArticleEvents::POST_CREATE, new ArticleEvent($article));

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
                $media = $mediaManager->handleUploadedFile(
                    $uploadedFile,
                    ArticleMedia::handleMediaId($mediaId)
                );

                return new SingleResourceResponse([
                    'media_id' => $mediaId,
                    'URL' => $mediaManager->getMediaPublicUrl($media),
                    'media' => base64_encode($mediaManager->getFile($media)),
                    'mime_type' => Mime::getMimeFromExtension($media->getFileExtension()),
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
        $media = $this->get('swp.repository.image')->getByCriteria(new Criteria([
            'assetId' => ArticleMedia::handleMediaId($mediaId),
        ]), [], 'am')->getQuery()->getOneOrNullResult();

        if (null === $media) {
            throw new NotFoundHttpException('Media don\'t exist in storage');
        }

        $mediaManager = $this->get('swp_content_bundle.manager.media');

        return new SingleResourceResponse([
            'media_id' => $mediaId,
            'URL' => $mediaManager->getMediaPublicUrl($media),
            'media' => base64_encode($mediaManager->getFile($media)),
            'mime_type' => Mime::getMimeFromExtension($media->getFileExtension()),
            'filemeta' => [],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return PackageInterface
     */
    private function handlePackage(Request $request): PackageInterface
    {
        $content = $request->getContent();
        $package = $this->get('swp_bridge.transformer.json_to_package')->transform($content);

        $packageRepository = $this->get('swp.repository.package');
        $existingPackage = $packageRepository->findOneBy(['guid' => $package->getGuid()]);
        if (null !== $existingPackage) {
            $packageRepository->remove($existingPackage);
        }
        $packageRepository->add($package);

        return $package;
    }
}
