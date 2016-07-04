<?php
/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\EventListener;

use SWP\Bundle\WebRendererBundle\Exception\NoThemeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof NoThemeException) {
            $response = new Response();
            $response->setContent('Please install default theme:</br>php app/console theme:setup -f</br>php app/console sylius:theme:assets:install');
            $event->setResponse($response);
        }
    }
}
