<?php

namespace SWP\TemplateEngineBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ContextListener
{
    protected $contextReader;

    protected $context;

    public function __construct($contextReader, $context)
    {
        $this->contextReader = $contextReader;
        $this->context = $context;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $contextAnnotation = $this->contextReader->read($event->getController());

        if (!is_null($contextAnnotation)) {
            $this->context->registerMeta($contextAnnotation->getName());
        }
    }
}
