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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class TenantNotFoundExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $isApiRequest = $event->getRequest()->attributes->get('_fos_rest_zone');
        if ($isApiRequest) {
            return;
        }

        $event->setResponse(new Response(
            $event->getException()->getMessage(),
            Response::HTTP_NOT_FOUND
        ));
    }
}
