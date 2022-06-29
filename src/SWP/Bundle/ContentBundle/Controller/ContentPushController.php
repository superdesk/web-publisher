<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Hoa\Mime\Mime;
use Psr\EventDispatcher\EventDispatcherInterface;
use SWP\Bundle\ContentBundle\Form\Type\MediaFileType;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Provider\FileProvider;
use SWP\Bundle\ContentBundle\Provider\FileProviderInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ContentPushMessage;
use SWP\Bundle\CoreBundle\Repository\PackageRepositoryInterface;
use SWP\Component\Bridge\Events;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use FOS\RestBundle\Controller\Annotations\Route;

class ContentPushController extends AbstractController {
  private EventDispatcherInterface $eventDispatcher;
  private FormFactoryInterface $formFactory;
  private MessageBusInterface $messageBus;
  private CachedTenantContextInterface $cachedTenantContext; //swp_multi_tenancy.tenant_context
  private DataTransformerInterface $dataTransformer; // swp_bridge.transformer.json_to_package
  private MediaManagerInterface $mediaManager; // swp_content_bundle.manager.media
  private EntityManagerInterface $entityManager; // swp.object_manager.media
  private PackageRepositoryInterface $packageRepository;//swp.repository.package
  private FileProviderInterface $fileProvider;

  /**
   * @param EventDispatcherInterface $eventDispatcher
   * @param FormFactoryInterface $formFactory
   * @param MessageBusInterface $messageBus
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param DataTransformerInterface $dataTransformer
   * @param MediaManagerInterface $mediaManager
   * @param EntityManagerInterface $entityManager
   * @param PackageRepositoryInterface $packageRepository
   * @param FileProviderInterface $fileProvider
   */
  public function __construct(EventDispatcherInterface $eventDispatcher, FormFactoryInterface $formFactory,
                              MessageBusInterface      $messageBus, CachedTenantContextInterface $cachedTenantContext,
                              DataTransformerInterface $dataTransformer, MediaManagerInterface $mediaManager,
                              EntityManagerInterface   $entityManager, PackageRepositoryInterface $packageRepository,
                              FileProviderInterface    $fileProvider) {
    $this->eventDispatcher = $eventDispatcher;
    $this->formFactory = $formFactory;
    $this->messageBus = $messageBus;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->dataTransformer = $dataTransformer;
    $this->mediaManager = $mediaManager;
    $this->entityManager = $entityManager;
    $this->packageRepository = $packageRepository;
    $this->fileProvider = $fileProvider;
  }


  /**
   * @Route("/api/{version}/content/push", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_push")
   */
  public function pushContentAction(Request $request): SingleResourceResponseInterface {
    $package = $this->dataTransformer->transform($request->getContent());
    $this->eventDispatcher->dispatch(new GenericEvent($package), Events::SWP_VALIDATION);

    $currentTenant = $this->cachedTenantContext->getTenant();

    $this->messageBus->dispatch(new ContentPushMessage($currentTenant->getId(), $request->getContent()));

    return new SingleResourceResponse(['status' => 'OK'], new ResponseContext(201));
  }

  /**
   * @Route("/api/{version}/assets/push", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_assets_push")
   */
  public function pushAssetsAction(Request $request): SingleResourceResponseInterface {
    $form = $this->formFactory->createNamed('', MediaFileType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $mediaManager = $this->mediaManager;
      $uploadedFile = $form->getData()['media'];
      $mediaId = $request->request->get('mediaId');

      if ($uploadedFile->isValid()) {
        $fileProvider = $this->fileProvider;
        $file = $fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $uploadedFile->guessExtension());

        if (null === $file) {
          $file = $mediaManager->handleUploadedFile($uploadedFile, $mediaId);
          $this->entityManager->flush();
        }

        return new SingleResourceResponse(
            [
                'media_id' => $mediaId,
                'URL' => $mediaManager->getMediaPublicUrl($file),
                'media' => base64_encode($mediaManager->getFile($file)),
                'mime_type' => Mime::getMimeFromExtension($file->getFileExtension()),
                'filemeta' => [],
            ],
            new ResponseContext(201)
        );
      }

      throw new \Exception('Uploaded file is not valid:' . $uploadedFile->getErrorMessage());
    }

    return new SingleResourceResponse($form);
  }

  /**
   * @Route("/api/{version}/assets/{action}/{mediaId}.{extension}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, requirements={"mediaId"=".+", "action"="get|push"}, name="swp_api_assets_get")
   */
  public function getAssetsAction(string $mediaId, string $extension): SingleResourceResponseInterface {
    $fileProvider = $this->fileProvider;
    $file = $fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $extension);

    if (null === $file) {
      throw new NotFoundHttpException('Media don\'t exist in storage');
    }

    $mediaManager = $this->mediaManager;

    return new SingleResourceResponse([
        'media_id' => $mediaId,
        'URL' => $mediaManager->getMediaPublicUrl($file),
        'media' => base64_encode($mediaManager->getFile($file)),
        'mime_type' => Mime::getMimeFromExtension($file->getFileExtension()),
        'filemeta' => [],
    ]);
  }

  protected function getPackageRepository() {
    return $this->packageRepository;
  }
}
