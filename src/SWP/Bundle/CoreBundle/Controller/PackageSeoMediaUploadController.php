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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\CoreBundle\Service\SeoImageUploaderInterface;
use SWP\Bundle\SeoBundle\Form\Type\SeoImageType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PackageSeoMediaUploadController extends AbstractController
{
    private $seoMetadataFactory;

    private $seoMetadataRepository;

    public function __construct(
        FactoryInterface $seoMetadataFactory,
        RepositoryInterface $seoMetadataRepository
    ) {
        $this->seoMetadataFactory = $seoMetadataFactory;
        $this->seoMetadataRepository = $seoMetadataRepository;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Uploads SEO image for Package",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\SeoBundle\Form\Type\SeoImageType"
     * )
     *
     * @Route("/api/{version}/packages/seo/upload/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_package_seo_image")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function uploadAction(Request $request, string $packageGuid, SeoImageUploaderInterface $seoImageUploader): SingleResourceResponse
    {
        $seoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);

        if (null === $seoMetadata) {
            $seoMetadata = $this->seoMetadataFactory->create();
        }

        $form = $this->get('form.factory')->createNamed('', SeoImageType::class, $seoMetadata);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $seoMetadata->setPackageGuid($packageGuid);
                $seoImageUploader->handleUpload($seoMetadata);

                $this->seoMetadataRepository->add($seoMetadata);
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => 'Could not upload an image.'], new ResponseContext(400));
            }

            return new SingleResourceResponse($seoMetadata, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
