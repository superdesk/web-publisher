<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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
     * @param ControllerResolverInterface $controllerResolver
     * @param UrlMatcherInterface         $urlMatcher
     */
    public function __construct(ControllerResolverInterface $controllerResolver, UrlMatcherInterface $urlMatcher)
    {
        $this->resolver = $controllerResolver;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return array
     */
    public function onKernelRequest(RequestEvent $event, $eventName, EventDispatcherInterface $dispatcher)
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

            // Assume that no resource is specified here if there is no path separator, because urlMatcher will return homepage
            if (false === strpos($resource, '/')) {
                continue;
            }
            $tempRequest = Request::create($resource);

            try {
                $route = $this->urlMatcher->match($tempRequest->getRequestUri());
            } catch (\Exception $e) {
                // If we don't have a matching route we return the original Link header
                continue;
            }

            $stubRequest->attributes->replace($route);
            $stubRequest->server = $event->getRequest()->server;
            $stubRequest::setTrustedProxies(['192.0.0.1', '10.0.0.0/8', $event->getRequest()->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_ALL);
            // Keep server name in sync with forwarded host
            if ($stubRequest->isFromTrustedProxy() && $stubRequest->server->has('HTTP_X_FORWARDED_HOST')) {
                $stubRequest->server->set('SERVER_NAME', $stubRequest->server->has('HTTP_X_FORWARDED_HOST'));
            }

            if (false === $controller = $this->resolver->getController($stubRequest)) {
                continue;
            }

            $subEvent = new ControllerEvent($event->getKernel(), $controller, $stubRequest, HttpKernelInterface::SUB_REQUEST);
            $kernelSubEvent = new RequestEvent($event->getKernel(), $stubRequest, HttpKernelInterface::SUB_REQUEST);
            $dispatcher->dispatch( $kernelSubEvent, KernelEvents::REQUEST);
            $dispatcher->dispatch( $subEvent, KernelEvents::CONTROLLER);
            $controller = $subEvent->getController();

            $argumentResolver = new ArgumentResolver();
            $arguments = $argumentResolver->getArguments($stubRequest, $controller);

            try {
                $result = call_user_func_array($controller, $arguments);
                // Our api returns objects for single resources
                if (!is_object($result)) {
                    continue;
                }

                // return clean object for LINK requests
                if ($result instanceof ResourcesListResponseInterface) {
                    $result = $result->getResources();
                } elseif ($result instanceof SingleResourceResponseInterface) {
                    $result = $result->getResource();
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
