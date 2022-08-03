<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\EventListener;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use SWP\Component\Common\Factory\KnpPaginatorRepresentationFactory;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\ResponseContextInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class ResourceResponseListener
{
    private $viewHandler;

    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    public function onKernelView(ViewEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        if (null === $controllerResult) {
            return;
        }

        /** @var ResponseContext $responseContext */
        $responseContext = $controllerResult->getResponseContext();
        if ($controllerResult instanceof ResourcesListResponseInterface) {
            if (ResponseContextInterface::INTENTION_API === $responseContext->getIntention()) {
                $factory = new KnpPaginatorRepresentationFactory();
                $representation = $factory->createRepresentation($controllerResult->getResources(), $event->getRequest());

                $view = View::create($representation, $responseContext->getStatusCode());
                $view = $this->setSerializationGroups($view, $responseContext->getSerializationGroups());

                $event->setResponse($this->viewHandler->handle(
                    $view
                ));
            }
        } elseif ($controllerResult instanceof SingleResourceResponseInterface) {
            if (ResponseContextInterface::INTENTION_API === $responseContext->getIntention()) {
                $view = View::create($controllerResult->getResource(), $responseContext->getStatusCode());
                $view = $this->setSerializationGroups($view, $responseContext->getSerializationGroups());

                $event->setResponse($this->viewHandler->handle(
                    $view
                ));
            }
        }

        $this->setHeaders($event, $responseContext);
        $this->clearCookies($event, $responseContext);
    }

    private function setHeaders(RequestEvent $event, ResponseContext $responseContext)
    {
        if (count($responseContext->getHeaders()) > 0) {
            $response = $event->getResponse();
            foreach ($responseContext->getHeaders() as $key => $value) {
                $response->headers->set($key, $value);
            }

            $event->setResponse($response);
        }
    }

    private function clearCookies(RequestEvent $event, ResponseContext $responseContext)
    {
        if (count($responseContext->getClearedCookies()) > 0) {
            $response = $event->getResponse();
            foreach ($responseContext->getClearedCookies() as $key) {
                $response->headers->clearCookie($key);
            }

            $event->setResponse($response);
        }
    }

    private function setSerializationGroups(View $view, array $serializationGroups): View
    {
        $context = new Context();
        $context->setGroups($serializationGroups);
        $context->enableMaxDepth();
        $view->setContext($context);

        return $view;
    }
}
