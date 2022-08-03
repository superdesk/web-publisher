<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Webhook Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebhookBundle\Controller;

use Doctrine\Persistence\ObjectManager;
use SWP\Bundle\WebhookBundle\Form\Type\WebhookType;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAPIController extends Controller {
  public function listWebhooks(EventDispatcherInterface $eventDispatcher, RepositoryInterface $repository,
                               Request         $request): ResourcesListResponse {
    $webhooks = $repository->getPaginatedByCriteria($eventDispatcher, new Criteria(), $request->query->all('sorting',), new PaginationData($request));

    return new ResourcesListResponse($webhooks);
  }

  public function getSingleWebhook(WebhookInterface $webhook): SingleResourceResponse {
    return new SingleResourceResponse($webhook);
  }

  public function createWebhook(
      RepositoryInterface  $ruleRepository,
      FactoryInterface     $ruleFactory,
      Request              $request,
      FormFactoryInterface $formFactory
  ): SingleResourceResponse {
    $webhook = $ruleFactory->create();
    $form = $formFactory->createNamed('', WebhookType::class, $webhook);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $ruleRepository->add($webhook);

      return new SingleResourceResponse($webhook, new ResponseContext(201));
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }

  public function deleteWebhook(RepositoryInterface $webhookRepository,
                                WebhookInterface    $webhook): SingleResourceResponse {
    $webhookRepository->remove($webhook);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  public function updateWebhook(
      ObjectManager        $objectManager,
      Request              $request,
      WebhookInterface     $webhook,
      FormFactoryInterface $formFactory
  ): SingleResourceResponse {
    $form = $formFactory->createNamed('', WebhookType::class, $webhook, ['method' => $request->getMethod()]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $objectManager->flush();
      $objectManager->refresh($webhook);

      return new SingleResourceResponse($webhook);
    }

    return new SingleResourceResponse($form, new ResponseContext(400));
  }
}
