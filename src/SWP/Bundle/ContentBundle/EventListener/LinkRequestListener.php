<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class LinkRequestListener
{
    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * @var UrlMatcherInterface
     */
    protected $urlMatcher;

    /**
     * @param ControllerResolverInterface $controllerResolver The 'controller_resolver' service
     * @param UrlMatcherInterface         $urlMatcher         The 'router' service
     */
    public function __construct(ControllerResolverInterface $controllerResolver, UrlMatcherInterface $urlMatcher)
    {
        $this->resolver = $controllerResolver;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        if (!$event->getRequest()->headers->has('link')) {
            return;
        }

        $links = [];
        $header = $event->getRequest()->headers->get('link');

        /*
         * Due to limitations, multiple same-name headers are sent as comma
         * separated values.
         *
         * This breaks those headers into Link headers following the format
         * http://tools.ietf.org/html/rfc2068#section-19.6.2.4
         */
        while (preg_match('/^((?:[^"]|"[^"]*")*?),/', $header, $matches)) {
            $header = trim(substr($header, strlen($matches[0])));
            $links[] = $matches[1];
        }

        if ($header) {
            $links[] = $header;
        }

        $requestMethod = $this->urlMatcher->getContext()->getMethod();

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();

        foreach ($links as $idx => $link) {
            // Force the GET method to avoid the use of the previous method (LINK/UNLINK)
            $this->urlMatcher->getContext()->setMethod('GET');

            $linkParams = explode(';', trim($link));
            $resourceType = null;
            if (count($linkParams) > 1) {
                $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                $resourceType = str_replace('"', '', str_replace('rel=', '', $resourceType));
            }
            $resource = array_shift($linkParams);
            $resource = preg_replace('/<|>/', '', $resource);
            $tempRequest = Request::create($resource);

            try {
                $route = $this->urlMatcher->match($tempRequest->getRequestUri());
            } catch (\Exception $e) {
                // If we don't have a matching route we return the original Link header
                continue;
            }

            $stubRequest->attributes->replace($route);
            $stubRequest->server = $event->getRequest()->server;
            if (false === $controller = $this->resolver->getController($stubRequest)) {
                continue;
            }

            $subEvent = new FilterControllerEvent($event->getKernel(), $controller, $stubRequest, HttpKernelInterface::SUB_REQUEST);
            $kernelSubEvent = new GetResponseEvent($event->getKernel(), $stubRequest, HttpKernelInterface::SUB_REQUEST);
            $dispatcher->dispatch(KernelEvents::REQUEST, $kernelSubEvent);
            $dispatcher->dispatch(KernelEvents::CONTROLLER, $subEvent);
            $controller = $subEvent->getController();

            $arguments = $this->resolver->getArguments($stubRequest, $controller);
            if (!isset($arguments[0])) {
                continue;
            }

            $arguments[0]->attributes->set('_link_request', true);
            try {
                $result = call_user_func_array($controller, $arguments);
                // Our api returns objects for single resources
                if (!is_object($result)) {
                    continue;
                }

                $links[$idx] = ['object' => $result, 'resourceType' => $resourceType];
            } catch (\Exception $e) {
                $links[$idx] = ['object' => $e, 'resourceType' => 'exception'];

                continue;
            }
        }

        $event->getRequest()->attributes->set('links', $links);
        $this->urlMatcher->getContext()->setMethod($requestMethod);

        return $links;
    }
}
