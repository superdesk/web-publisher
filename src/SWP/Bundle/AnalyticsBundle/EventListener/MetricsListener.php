<?php

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\EventListener;

use SWP\Bundle\AnalyticsBundle\Model\RequestMetric;
use SWP\Bundle\AnalyticsBundle\Repository\RequestMetricRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class MetricsListener
{
    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * @var StopwatchEvent
     */
    protected $stopwatchEvent;

    /**
     * @var RequestMetricRepository
     */
    protected $requestMetricRepository;

    public function setRequestMetricRepository(RequestMetricRepository $requestMetricRepository)
    {
        $this->requestMetricRepository = $requestMetricRepository;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $event->getRequest()->get('_route');

        // Ignore web debug toolbar
        if (null !== $route && '_wdt' !== $route) {
            $this->stopwatch = new Stopwatch();
            $this->stopwatch->start($event->getRequest()->getUri());
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Route is null here in test environment
        $route = $event->getRequest()->get('_route');
        if (null === $route) {
            return;
        }

        if (null === $this->stopwatch) {
            return;
        }

        $requestUri = $event->getRequest()->getUri();

        // find out if this is a profiler request, if not don't analyze it
        try {
            $this->stopwatchEvent = $this->stopwatch->stop($requestUri);
        } catch (\LogicException $e) {
            // do nothing, we shouldn't be analyzing this controller
            return false;
        }

        $duration = $this->stopwatchEvent->getDuration();
        $requestMetric = new RequestMetric();
        $requestMetric
            ->setUri($requestUri)
            ->setDuration($duration)
            ->setRoute($route);
        $this->requestMetricRepository->persistAndFlush($requestMetric);
    }
}
