<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\AnalyticsBundle\EventListener;

use Symfony\Bridge\Monolog\Processor\WebProcessor;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use SWP\Bundle\AnalyticsBundle\Controller\AnalyzedControllerInterface;

class RenderListener extends WebProcessor
{

    protected $stopwatch;

    protected $stopwatchEvent;

    protected $container;

    protected $logger;

    protected $currentPage;

    protected $currentTemplate;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
        $this->stopwatchEvent = null;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get('monolog.logger.analytics');
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        // find out if this is a profiler request, if not don't analyze it
        if ($controller[0] instanceof AnalyzedControllerInterface) {
            $this->stopwatch->start($event->getRequest()->getUri());
            $this->stopwatchEvent = null;
        }
    }

    /**
     * triggered when a controller returns anything other than a Response object.
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $requestUri = $event->getRequest()->getUri();
        $this->currentPage = $this->container->get('context')->getCurrentPage();
        $this->currentTemplate = 'how the heck do I get the $view that was passed during Controller->render()';
 
        // find out if this is a profiler request, if not don't analyze it
        try {
            $this->stopwatchEvent = $this->stopwatch->stop($requestUri);
        } catch (\LogicException $e) {
            // do nothing, we shouldn't be analyzing this controller
            return false;
        }
        $this->logger->error('view event');
    }

    /**
     * triggered when a controller returns a Response object
     * this is default for Controller->render().
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $requestUri = $event->getRequest()->getUri();
        $this->currentPage = $this->container->get('context')->getCurrentPage();
        $this->currentTemplate = 'how the heck do I get the $view that was passed during Controller->render()';
 
        // find out if this is a profiler request, if not don't analyze it
        try {
            $this->stopwatchEvent = $this->stopwatch->stop($requestUri);
        } catch (\LogicException $e) {
            // do nothing, we shouldn't be analyzing this controller
            return false;
        }
        $this->logger->debug('response event');
    }

    public function processRecord(array $record)
    {
        $record['duration'] = ($this->stopwatchEvent) ? $this->stopwatchEvent->getDuration() : 0;
        $record['memory'] = ($this->stopwatchEvent) ? $this->stopwatchEvent->getMemory() : 0;
        $record['template'] = ($this->currentTemplate) ? $this->currentTemplate : '';

        return $record;
    }

}
