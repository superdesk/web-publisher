<?php

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SWP\Bundle\WebhookBundle\Controller\AbstractAPIController;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Bundle\WebhookBundle\Repository\WebhookRepositoryInterface;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractAPIController {
  private WebhookRepositoryInterface $webhookRepository;
  private FormFactoryInterface $formFactory;
  private FactoryInterface $webhookFactory;
  private EntityManagerInterface $entityManager;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param WebhookRepositoryInterface $webhookRepository
   * @param FormFactoryInterface $formFactory
   * @param FactoryInterface $webhookFactory
   * @param EntityManagerInterface $entityManager
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(WebhookRepositoryInterface $webhookRepository, FormFactoryInterface $formFactory,
                              FactoryInterface           $webhookFactory, EntityManagerInterface $entityManager,
                              EventDispatcherInterface            $eventDispatcher) {
    $this->webhookRepository = $webhookRepository;
    $this->formFactory = $formFactory;
    $this->webhookFactory = $webhookFactory;
    $this->entityManager = $entityManager;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_list_webhook")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    return $this->listWebhooks($this->eventDispatcher,$this->webhookRepository, $request);
  }

  /**
   * @Route("/api/{version}/webhooks/{id}", requirements={"id"="\d+"}, options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_webhook")
   *
   * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
   */
  public function getAction(WebhookInterface $webhook): SingleResourceResponseInterface {
    return $this->getSingleWebhook($webhook);
  }

  /**
   * @Route("/api/{version}/webhooks/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_core_create_webhook")
   */
  public function createAction(Request $request): SingleResourceResponseInterface {
    $ruleRepository = $this->webhookRepository;
    $ruleFactory = $this->webhookFactory;
    $formFactory = $this->formFactory;

    return $this->createWebhook($ruleRepository, $ruleFactory, $request, $formFactory);
  }

  /**
   * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_core_delete_webhook", requirements={"id"="\d+"})
   *
   * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
   */
  public function deleteAction(WebhookInterface $webhook): SingleResourceResponseInterface {
    $webhookRepository = $this->webhookRepository;

    return $this->deleteWebhook($webhookRepository, $webhook);
  }

  /**
   * @Route("/api/{version}/webhooks/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PATCH"}, name="swp_api_core_update_webhook", requirements={"id"="\d+"})
   *
   * @ParamConverter("webhook", class="SWP\Bundle\WebhookBundle\Model\Webhook")
   */
  public function updateAction(Request $request, WebhookInterface $webhook): SingleResourceResponseInterface {
    $objectManager = $this->entityManager;
    $formFactory = $this->formFactory;

    return $this->updateWebhook($objectManager, $request, $webhook, $formFactory);
  }
}
