<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpCacheHeaderListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var Route $routeObject */
        $routeObject = $event->getRequest()->get('routeDocument');

        if (null !== $routeObject) {
            // Get expiry time
            $cacheTimeInSeconds = $routeObject->getCacheTimeInSeconds();
            if (0 < $cacheTimeInSeconds) {
                $response = $event->getResponse();
                $response->setMaxAge($cacheTimeInSeconds);
                $response->setSharedMaxAge($cacheTimeInSeconds);
                $response->setPublic();
            }
        }
    }
}
