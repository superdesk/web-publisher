<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
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
    protected MediaManagerInterface $mediaManager;

    /**
     * @param FormFactoryInterface $formFactory
     * @param FactoryInterface $seoMetadataFactory
     * @param RepositoryInterface $seoMetadataRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param MediaManagerInterface $mediaManager
     */
  public function __construct(
      FormFactoryInterface $formFactory,
      FactoryInterface $seoMetadataFactory,
      RepositoryInterface  $seoMetadataRepository,
      EventDispatcherInterface $eventDispatcher,
      MediaManagerInterface $mediaManager
  ) {
    $this->formFactory = $formFactory;
    $this->seoMetadataFactory = $seoMetadataFactory;
    $this->seoMetadataRepository = $seoMetadataRepository;
    $this->eventDispatcher = $eventDispatcher;
      $this->mediaManager = $mediaManager;
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

      $seoMetadata = $this->seoMetadataRepository->findOneByPackageGuid($packageGuid);
    if (null === $seoMetadata) {
      throw new NotFoundHttpException('SEO metadata not found!');
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

    return new SingleResourceResponse($response, new ResponseContext(200));
  }
}
