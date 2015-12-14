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
namespace SWP\AnalyticsBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class BacktraceLoggerListener
{
    private $_logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->_logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->_logger->addError($event->getException());
    }
}
