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

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use SWP\Component\Common\Factory\KnpPaginatorRepresentationFactory;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\ResponseContextInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

final class ResourceResponseListener
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * ResourcesListResponseListener constructor.
     *
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        if (null === $controllerResult) {
            return;
        }

        /** @var ResponseContext $responseContext */
        $responseContext = $controllerResult->getResponseContext();
        if ($controllerResult instanceof ResourcesListResponseInterface) {
            if ($responseContext->getIntention() === ResponseContextInterface::INTENTION_API) {
                $factory = new KnpPaginatorRepresentationFactory();
                $representation = $factory->createRepresentation($controllerResult->getResources(), $event->getRequest());
                $event->setResponse($this->viewHandler->handle(
                    View::create($representation, $responseContext->getStatusCode())
                ));
            }
        } elseif ($controllerResult instanceof SingleResourceResponseInterface) {
            if ($responseContext->getIntention() === ResponseContextInterface::INTENTION_API) {
                $event->setResponse($this->viewHandler->handle(
                    View::create($controllerResult->getResource(), $responseContext->getStatusCode())
                ));
            }
        }

        $this->setHeaders($event, $responseContext);
        $this->clearCookies($event, $responseContext);
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @param ResponseContext                     $responseContext
     */
    protected function setHeaders(GetResponseForControllerResultEvent $event, ResponseContext $responseContext)
    {
        if (count($responseContext->getHeaders()) > 0) {
            $response = $event->getResponse();
            foreach ($responseContext->getHeaders() as $key => $value) {
                $response->headers->set($key, $value);
            }

            $event->setResponse($response);
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @param ResponseContext                     $responseContext
     */
    protected function clearCookies(GetResponseForControllerResultEvent $event, ResponseContext $responseContext)
    {
        if (count($responseContext->getClearedCookies()) > 0) {
            $response = $event->getResponse();
            foreach ($responseContext->getClearedCookies() as $key) {
                $response->headers->clearCookie($key);
            }

            $event->setResponse($response);
        }
    }
}
