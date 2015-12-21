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
