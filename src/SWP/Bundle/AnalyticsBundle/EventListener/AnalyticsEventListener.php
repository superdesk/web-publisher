<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\EventListener;

use SWP\Bundle\AnalyticsBundle\Messenger\AnalyticsEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class AnalyticsEventListener
{
    const TERMINATE_IMMEDIATELY = 'terminate-immediately';

    const EVENT_ENDPOINT = '_swp_analytics';

    /** @var MessageBusInterface */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), self::EVENT_ENDPOINT) &&
            in_array($request->getMethod(), ['POST', 'GET'])
        ) {
            $httpReferrer = $request->server->get('HTTP_REFERER', $request->query->get('host', $request->getHost()));

            $this->messageBus->dispatch(new AnalyticsEvent(
                $httpReferrer,
                $request->query->get('articleId', null),
                $request->query->get('ref', null)
            ));

            $response = new Response();
            $response->headers->add([self::TERMINATE_IMMEDIATELY => true]);
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($response->headers->has(self::TERMINATE_IMMEDIATELY)) {
            $event->stopPropagation();
        }
    }

    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), self::EVENT_ENDPOINT)) {
            $event->stopPropagation();
        }
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response->headers->has(self::TERMINATE_IMMEDIATELY)) {
            $event->stopPropagation();
        }
    }
}
