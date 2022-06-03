<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class PreviewModeListener.
 */
class PreviewModeListener
{
    /**
     * @var Context
     */
    protected $templateContext;

    public function __construct(Context $templateContext)
    {
        $this->templateContext = $templateContext;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        if ('swp_package_preview' === $event->getRequest()->attributes->get('_route')) {
            $this->templateContext->setPreviewMode(true);
        }
    }
}
