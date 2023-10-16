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

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\CoreBundle\Service\SeoImageUploaderInterface;
use SWP\Bundle\SeoBundle\Form\Type\SeoImageType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class PackageSeoMediaUploadController extends AbstractController
{
    private FactoryInterface $seoMetadataFactory;
    private RepositoryInterface $seoMetadataRepository;
    private FormFactoryInterface $formFactory;
    protected MediaManagerInterface $mediaManager;

    /**
     * @param FactoryInterface $seoMetadataFactory
     * @param RepositoryInterface $seoMetadataRepository
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        FactoryInterface      $seoMetadataFactory,
        RepositoryInterface   $seoMetadataRepository,
        FormFactoryInterface  $formFactory,
        MediaManagerInterface $mediaManager,
    )
    {
        $this->seoMetadataFactory = $seoMetadataFactory;
        $this->seoMetadataRepository = $seoMetadataRepository;
        $this->formFactory = $formFactory;
        $this->mediaManager = $mediaManager;
    }


    /**
     * @Route("/api/{version}/packages/seo/upload/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_package_seo_image")
     */
    public function uploadAction(
        Request $request,
        string $packageGuid,
        SeoImageUploaderInterface $seoImageUploader
    ): SingleResourceResponse {
        $seoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);

        if (null === $seoMetadata) {
            $seoMetadata = $this->seoMetadataFactory->create();
        }


        $form = $this->formFactory->createNamed('', SeoImageType::class, $seoMetadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $seoMetadata->setPackageGuid($packageGuid);
                $seoImageUploader->handleUpload($seoMetadata);

                $this->seoMetadataRepository->add($seoMetadata);
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => 'Could not upload an image.'], new ResponseContext(400));
            }

            $response = [
                "meta_title" => $seoMetadata->getMetaTitle(),
                "meta_description" => $seoMetadata->getMetaDescription(),
                "og_title" => $seoMetadata->getOgTitle(),
                "og_description" => $seoMetadata->getOgDescription(),
                "twitter_title" => $seoMetadata->getTwitterTitle(),
                "twitter_description" => $seoMetadata->getTwitterDescription(),
            ];

            if ($seoMetadata->getMetaMedia()) {
                $metaImage = $seoMetadata->getMetaMedia()->getImage();
                $response['_links']['meta_media_url'] = [ 'href' => $this->mediaManager->getMediaPublicUrl($metaImage)];
            }
            if ($seoMetadata->getOgMedia()) {
                $metaImage = $seoMetadata->getOgMedia()->getImage();
                $response['_links']['og_media_url'] = [ 'href' => $this->mediaManager->getMediaPublicUrl($metaImage)];
            }
            if ($seoMetadata->getTwitterMedia()) {
                $metaImage = $seoMetadata->getTwitterMedia()->getImage();
                $response['_links']['twitter_media_url'] = [ 'href' => $this->mediaManager->getMediaPublicUrl($metaImage)];
            }

            return new SingleResourceResponse($response, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
