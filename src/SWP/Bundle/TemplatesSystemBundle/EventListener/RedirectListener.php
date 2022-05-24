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

namespace SWP\Bundle\TemplatesSystemBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Class RedirectListener.
 */
class RedirectListener
{
    /**
     * Check if request have attribute for redirection.
     * if it exists - perform redirect.
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        // Redirect request to url set in template
        if ($request->attributes->has('_swp_redirect')) {
            $redirectData = $request->attributes->get('_swp_redirect');
            $request->attributes->remove('_swp_redirect');
            if ($request->getUri() === $redirectData['url']) {
                // Prevent redirect loops
                return;
            }

            $event->setResponse(new RedirectResponse($redirectData['url'], $redirectData['code']));
        }

        // Redirect request to 404 error page
        if ($request->attributes->has('_swp_not_found')) {
            $message = $request->attributes->get('_swp_not_found');
            $request->attributes->remove('_swp_not_found');

            throw new NotFoundHttpException($message);
        }
    }
}
