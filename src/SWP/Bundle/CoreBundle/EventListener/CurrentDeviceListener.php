<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\CoreBundle\Detection\DeviceDetectionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CurrentDeviceListener
{
    /**
     * @var DeviceDetectionInterface
     */
    private $deviceDetection;

    /**
     * @param DeviceDetectionInterface $deviceDetection
     */
    public function __construct(DeviceDetectionInterface $deviceDetection)
    {
        $this->deviceDetection = $deviceDetection;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        $this->deviceDetection->setUserAgent($event->getRequest()->headers->get('User-Agent'));
    }
}
