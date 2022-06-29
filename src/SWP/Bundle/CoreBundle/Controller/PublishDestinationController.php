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

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Form\Type\PublishDestinationType;
use SWP\Bundle\CoreBundle\Model\PublishDestinationInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class PublishDestinationController extends Controller {

  private FormFactoryInterface $formFactory;
  private EventDispatcherInterface $eventDispatcher;
  private CachedTenantContextInterface $cachedTenantContext;
  private RepositoryInterface $publishDestinationRepository;
  private EntityManagerInterface $entityManager;
  private FactoryInterface $publishDestinationFactory;

  /**
   * @param FormFactoryInterface $formFactory
   * @param EventDispatcherInterface $eventDispatcher
   * @param CachedTenantContextInterface $cachedTenantContext
   * @param RepositoryInterface $publishDestinationRepository
   * @param EntityManagerInterface $entityManager
   * @param FactoryInterface $publishDestinationFactory
   */
  public function __construct(FormFactoryInterface         $formFactory,
                              EventDispatcherInterface     $eventDispatcher,
                              CachedTenantContextInterface $cachedTenantContext,
                              RepositoryInterface          $publishDestinationRepository,
                              EntityManagerInterface       $entityManager,
                              FactoryInterface             $publishDestinationFactory) {
    $this->formFactory = $formFactory;
    $this->eventDispatcher = $eventDispatcher;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->publishDestinationRepository = $publishDestinationRepository;
    $this->entityManager = $entityManager;
    $this->publishDestinationFactory = $publishDestinationFactory;
  }

  /**
   * @Route("/api/{version}/organization/destinations/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_publishing_destination_create")
   */
  public function createAction(Request $request): SingleResourceResponse {
    $tenantContext = $this->cachedTenantContext;

    $this->eventDispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);

    $destination = $this->publishDestinationFactory->create();
    $form = $this->formFactory->createNamed('', PublishDestinationType::class, $destination, ['method' => $request->getMethod()]);
    $currentOrganization = $tenantContext->getTenant()->getOrganization();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $repository = $this->publishDestinationRepository;
      /** @var PublishDestinationInterface $publishDestination */
      $publishDestination = $repository->findOneByTenant($destination->getTenant());
      if (null !== $publishDestination) {
        $repository->remove($publishDestination);
      }

      $currentOrganization->addPublishDestination($destination);
      $this->entityManager->flush();

      return new SingleResourceResponse($destination, new ResponseContext(200));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  /**
   * @Route("/api/{version}/organization/destinations/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_publishing_destination_update", requirements={"id"="\d+"})
   * @ParamConverter("publishDestination", class="SWP\Bundle\CoreBundle\Model\PublishDestination")
   */
  public function updateAction(Request                     $request,
                               PublishDestinationInterface $publishDestination): SingleResourceResponse {
    $objectManager = $this->entityManager;

    $form = $this->formFactory->createNamed('', PublishDestinationType::class, $publishDestination, [
        'method' => $request->getMethod(),
    ]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $objectManager->flush();
      $objectManager->refresh($publishDestination);

      return new SingleResourceResponse($publishDestination);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }
}
