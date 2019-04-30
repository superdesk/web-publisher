<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\ContentBundle\Controller\AbstractMediaController;
use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\FileProviderInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Service\SeoImageUploaderInterface;
use SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SeoMediaController extends AbstractMediaController
{
    private $seoMetadataFactory;

    private $articleProvider;

    private $seoObjectManager;

    public function __construct(
        FactoryInterface $seoMetadataFactory,
        ArticleProviderInterface $articleProvider,
        ObjectManager $seoObjectManager,
        MediaManagerInterface $seoMediaManager,
        Cache $cacheProvider,
        FileProviderInterface $fileProvider,
        FileExtensionCheckerInterface $fileExtensionChecker
    ) {
        $this->seoMetadataFactory = $seoMetadataFactory;
        $this->articleProvider = $articleProvider;
        $this->seoObjectManager = $seoObjectManager;

        parent::__construct($seoMediaManager, $cacheProvider, $fileProvider, $fileExtensionChecker);
    }

    /**
     * @Route("/media/seo/{mediaId}.{extension}", methods={"GET"}, options={"expose"=true}, requirements={"mediaId"=".+"}, name="swp_seo_media_get")
     */
    public function getAction(string $mediaId, string $extension): Response
    {
        return $this->getMedia($mediaId, $extension);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Uploads SEO image",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\ImageUploadType"
     * )
     * @Route("/api/{version}/upload/seo_image/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_seo_image")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function uploadSeoImageAction(Request $request, string $id, SeoImageUploaderInterface $seoImageUploader): SingleResourceResponse
    {
        $article = $this->findOr404($id);
        $seoMetadata = $article->getSeoMetadata();

        if (null === $seoMetadata) {
            $seoMetadata = $this->seoMetadataFactory->create();
        }

        $form = $this->get('form.factory')->createNamed('', SeoMetadataType::class, $seoMetadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $seoImageUploader->handleUpload($seoMetadata);

                if (null === $article->getSeoMetadata()) {
                    $article->setSeoMetadata($seoMetadata);
                }

                $this->seoObjectManager->flush();
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => 'Could not upload an image.'], new ResponseContext(400));
            }

            return new SingleResourceResponse($seoMetadata, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404(string $id): ArticleInterface
    {
        if (null === $article = $this->articleProvider->getOneById($id)) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
