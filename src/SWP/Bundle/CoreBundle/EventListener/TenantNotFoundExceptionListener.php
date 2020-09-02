<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class TenantNotFoundExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $isApiRequest = $event->getRequest()->attributes->get('_fos_rest_zone');
        $exception = $event->getThrowable();
        if ($isApiRequest || !$exception instanceof TenantNotFoundException) {
            return;
        }

        $event->setResponse(new Response(
            $exception->getMessage(),
            Response::HTTP_NOT_FOUND
        ));
    }
}
