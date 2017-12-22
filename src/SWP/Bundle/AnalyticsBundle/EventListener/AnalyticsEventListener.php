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

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

/**
 * Class AnalyticsEventListener.
 */
class AnalyticsEventListener
{
    const TERMINATE_IMIDEDIATELY = 'terminate-imidediately';

    const EVENT_ENDPOINT = '_swp_analytics';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * AnalyticsEventListener constructor.
     *
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), self::EVENT_ENDPOINT)) {
            $this->producer->publish(serialize($request));

            $response = new Response();
            $response->headers->add(['terminate-imidediately' => true]);
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($response->headers->has(self::TERMINATE_IMIDEDIATELY)) {
            $event->stopPropagation();
        }
    }

    /**
     * @param FinishRequestEvent $event
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), self::EVENT_ENDPOINT)) {
            $event->stopPropagation();
        }
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response->headers->has(self::TERMINATE_IMIDEDIATELY)) {
            $event->stopPropagation();
        }
    }
}
