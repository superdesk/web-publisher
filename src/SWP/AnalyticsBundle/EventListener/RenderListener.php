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
namespace SWP\AnalyticsBundle\EventListener;

use Symfony\Bridge\Monolog\Processor\WebProcessor;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Stopwatch\Stopwatch; 

use SWP\AnalyticsBundle\Controller\AnalyzedControllerInterface;

class RenderListener extends WebProcessor
{

    protected $_stopwatch;

    protected $_stopwatchEvent;

    protected $_container;

    protected $_logger;

    protected $_currentPage;

    protected $_currentTemplate;

    public function __construct()
    {
        $this->_stopwatch = new Stopwatch();
        $this->_stopwatchEvent = null;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->_container = $container;
        $this->_logger = $container->get('monolog.logger.analytics'); 
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
            $this->_stopwatch->start($event->getRequest()->getUri());
            $this->_stopwatchEvent = null;
        }
    }

    /**
     * triggered when a controller returns anything other than a Response object
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $requestUri = $event->getRequest()->getUri();
        $this->_currentPage = $this->_container->get('context')->getCurrentPage();
        $this->_currentTemplate = 'how the heck do I get the $view that was passed during Controller->render()';
 
        // find out if this is a profiler request, if not don't analyze it
        try {
            $this->_stopwatchEvent = $this->_stopwatch->stop($requestUri);
        } catch (\LogicException $e) {
            // do nothing, we shouldn't be analyzing this controller
            return false;
        }
        $this->_logger->error('view event');
    }

    /**
     * triggered when a controller returns a Response object
     * this is default for Controller->render()
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $requestUri = $event->getRequest()->getUri();
        $this->_currentPage = $this->_container->get('context')->getCurrentPage();
        $this->_currentTemplate = 'how the heck do I get the $view that was passed during Controller->render()';
 
        // find out if this is a profiler request, if not don't analyze it
        try {
            $this->_stopwatchEvent = $this->_stopwatch->stop($requestUri);
        } catch (\LogicException $e) {
            // do nothing, we shouldn't be analyzing this controller
            return false;
        }
        $this->_logger->error('response event');
    }

    public function processRecord(array $record)
    {
        $record['duration'] = ($this->_stopwatchEvent) ? $this->_stopwatchEvent->getDuration() : 0;
        $record['memory'] = ($this->_stopwatchEvent) ? $this->_stopwatchEvent->getMemory() : 0;
        $record['template'] = ($this->_currentTemplate) ? $this->_currentTemplate : '';

        return $record;
    }

}
