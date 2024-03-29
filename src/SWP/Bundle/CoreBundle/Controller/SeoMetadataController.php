<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\SeoBundle\Form\Type\SeoMetadataType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\Route;

class SeoMetadataController extends AbstractController {
  private FormFactoryInterface $formFactory;
  private FactoryInterface $seoMetadataFactory;
  private RepositoryInterface $seoMetadataRepository;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param FormFactoryInterface $formFactory
   * @param FactoryInterface $seoMetadataFactory
   * @param RepositoryInterface $seoMetadataRepository
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(FormFactoryInterface $formFactory, FactoryInterface $seoMetadataFactory,
                              RepositoryInterface  $seoMetadataRepository, EventDispatcherInterface $eventDispatcher) {
    $this->formFactory = $formFactory;
    $this->seoMetadataFactory = $seoMetadataFactory;
    $this->seoMetadataRepository = $seoMetadataRepository;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * @Route("/api/{version}/packages/seo/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PUT"}, name="swp_api_core_seo_metadata_put")
   */
  public function put(Request $request, string $packageGuid): SingleResourceResponse {
    $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

    $seoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
    if (null === $seoMetadata) {
      $seoMetadata = $this->seoMetadataFactory->create();
    }

    $form = $this->formFactory->createNamed('', SeoMetadataType::class, $seoMetadata, ['method' => $request->getMethod()]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $seoMetadata->setPackageGuid($packageGuid);
      $this->seoMetadataRepository->add($seoMetadata);

      return new SingleResourceResponse($seoMetadata, new ResponseContext(200));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/packages/seo/{packageGuid}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_seo_metadata_get")
   */
  public function getAction(string $packageGuid): SingleResourceResponse {
    $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

    $existingSeoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
    if (null === $existingSeoMetadata) {
      throw new NotFoundHttpException('SEO metadata not found!');
    }

    return new SingleResourceResponse($existingSeoMetadata, new ResponseContext(200));
  }
}
