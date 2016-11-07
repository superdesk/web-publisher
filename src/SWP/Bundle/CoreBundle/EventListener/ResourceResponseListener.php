<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use SWP\Component\Common\Factory\KnpPaginatorRepresentationFactory;
use SWP\Bundle\CoreBundle\Response\ResourcesListResponseInterface;
use SWP\Bundle\CoreBundle\Response\ResponseContextInterface;
use SWP\Bundle\CoreBundle\Response\SingleResourceResponseInterface;
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

        if ($controllerResult instanceof ResourcesListResponseInterface) {
            $responseContext = $controllerResult->getResponseContext();

            if ($responseContext->getIntention() === ResponseContextInterface::INTENTION_API) {
                $factory = new KnpPaginatorRepresentationFactory();
                $representation = $factory->createRepresentation($controllerResult->getResources(), $event->getRequest());

                $event->setResponse($this->viewHandler->handle(
                    View::create($representation, $responseContext->getStatusCode())
                ));
            }
        } elseif ($controllerResult instanceof SingleResourceResponseInterface) {
            $responseContext = $controllerResult->getResponseContext();

            if ($responseContext->getIntention() === ResponseContextInterface::INTENTION_API) {
                $event->setResponse($this->viewHandler->handle(
                    View::create($controllerResult->getResource(), $responseContext->getStatusCode())
                ));
            }
        }
    }
}
