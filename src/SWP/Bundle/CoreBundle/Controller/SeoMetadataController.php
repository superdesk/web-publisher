<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\CoreBundle\Service\SeoImageUploaderInterface;
use SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;

class SeoMetadataController extends AbstractController
{
    private $seoMetadataFactory;

    private $seoMetadataRepository;

    private $seoMetadataObjectManager;

    private $eventDispatcher;

    public function __construct(
        FactoryInterface $seoMetadataFactory,
        RepositoryInterface $seoMetadataRepository,
        ObjectManager $seoMetadataObjectManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->seoMetadataFactory = $seoMetadataFactory;
        $this->seoMetadataRepository = $seoMetadataRepository;
        $this->seoMetadataObjectManager = $seoMetadataObjectManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Add a new SEO metadata entry",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType"
     * )
     *
     * @Route("/api/{version}/seo/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_seo_metadata_create")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function create(Request $request, SeoImageUploaderInterface $seoImageUploader): SingleResourceResponse
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $seoMetadata = $this->seoMetadataFactory->create();
        $form = $form = $this->get('form.factory')->createNamed('', SeoMetadataType::class, $seoMetadata, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingSeoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($seoMetadata->getPackageGuid());
            if (null !== $existingSeoMetadata) {
                $this->seoMetadataRepository->remove($existingSeoMetadata);
            }

            $seoImageUploader->handleUpload($seoMetadata);

            $this->seoMetadataObjectManager->persist($seoMetadata);
            $this->seoMetadataObjectManager->flush();

            return new SingleResourceResponse($seoMetadata, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Edits SEO metadata entry",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     },
     *     input="SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType"
     * )
     *
     * @Route("/api/{version}/seo/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_seo_metadata_edit")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function edit(Request $request, string $packageGuid, SeoImageUploaderInterface $seoImageUploader): SingleResourceResponse
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $existingSeoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
        if (null === $existingSeoMetadata) {
            throw new NotFoundHttpException('SEO metadata not found!');
        }

        $form = $form = $this->get('form.factory')->createNamed('', SeoMetadataType::class, $existingSeoMetadata, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seoImageUploader->handleUpload($existingSeoMetadata);

            $this->seoMetadataObjectManager->flush();

            return new SingleResourceResponse($existingSeoMetadata, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
