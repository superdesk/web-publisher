<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @Route("/api/{version}/packages/{id}/seo/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_seo_metadata_create")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function createAction(Request $request, string $id): SingleResourceResponse
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

            $seoMetadata->setPackageGuid($id);
            $this->seoMetadataObjectManager->persist($seoMetadata);
            $this->seoMetadataObjectManager->flush();

            return new SingleResourceResponse($seoMetadata, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
