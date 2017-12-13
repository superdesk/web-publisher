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

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\WebhookBundle\Form\Type\WebhookType;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use SWP\Component\Common\Exception\NotFoundHttpException;

/**
 * Class AbstractAPIController.
 */
abstract class AbstractAPIController extends Controller
{
    /**
     * @param RepositoryInterface $repository
     * @param Request             $request
     *
     * @throws NotFoundHttpException
     *
     * @return ResourcesListResponse
     */
    public function listWebhooks(RepositoryInterface $repository, Request $request)
    {
        $webhooks = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        if (0 === $webhooks->count()) {
            throw new NotFoundHttpException('No webhooks were found.');
        }

        return new ResourcesListResponse($webhooks);
    }

    /**
     * @param WebhookInterface $webhook
     *
     * @return SingleResourceResponse
     */
    public function getSingleWebhook(WebhookInterface $webhook)
    {
        return new SingleResourceResponse($webhook);
    }

    /**
     * @param RepositoryInterface  $ruleRepository
     * @param FactoryInterface     $ruleFactory
     * @param Request              $request
     * @param FormFactoryInterface $formFactory
     *
     * @return SingleResourceResponse
     */
    public function createWebhook(RepositoryInterface $ruleRepository, FactoryInterface $ruleFactory, Request $request, FormFactoryInterface $formFactory)
    {
        $webhook = $ruleFactory->create();
        $form = $formFactory->create(WebhookType::class, $webhook);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ruleRepository->add($webhook);

            return new SingleResourceResponse($webhook, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @param RepositoryInterface $webhookRepository
     * @param WebhookInterface    $webhook
     *
     * @return SingleResourceResponse
     */
    public function deleteWebhook(RepositoryInterface $webhookRepository, WebhookInterface $webhook)
    {
        $webhookRepository->remove($webhook);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @param ObjectManager        $objectManager
     * @param Request              $request
     * @param WebhookInterface     $webhook
     * @param FormFactoryInterface $formFactory
     *
     * @return SingleResourceResponse
     */
    public function updateWebhook(ObjectManager $objectManager, Request $request, WebhookInterface $webhook, FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(WebhookType::class, $webhook, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $objectManager->flush();
            $objectManager->refresh($webhook);

            return new SingleResourceResponse($webhook);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
