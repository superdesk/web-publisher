<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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

    private $eventDispatcher;

    public function __construct(
        FactoryInterface $seoMetadataFactory,
        RepositoryInterface $seoMetadataRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->seoMetadataFactory = $seoMetadataFactory;
        $this->seoMetadataRepository = $seoMetadataRepository;
        $this->eventDispatcher = $eventDispatcher;
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
     * @Route("/api/{version}/packages/seo/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PUT"}, name="swp_api_core_seo_metadata_put")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function put(Request $request, string $packageGuid): SingleResourceResponse
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $seoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
        if (null === $seoMetadata) {
            $seoMetadata = $this->seoMetadataFactory->create();
        }

        $form = $form = $this->get('form.factory')->createNamed('', SeoMetadataType::class, $seoMetadata, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seoMetadata->setPackageGuid($packageGuid);
            $this->seoMetadataRepository->add($seoMetadata);

            return new SingleResourceResponse($seoMetadata, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Gets SEO metadata entry",
     *     statusCodes={
     *         201="Returned on success.",
     *         400="Returned when form have errors"
     *     }
     * )
     *
     * @Route("/api/{version}/packages/seo/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_seo_metadata_get")
     *
     * @return SingleResourceResponse
     */
    public function getAction(string $packageGuid): SingleResourceResponse
    {
        $this->eventDispatcher->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);

        $existingSeoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
        if (null === $existingSeoMetadata) {
            throw new NotFoundHttpException('SEO metadata not found!');
        }

        return new SingleResourceResponse($existingSeoMetadata, new ResponseContext(200));
    }
}
