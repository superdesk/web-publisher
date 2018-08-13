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

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use SWP\Component\Storage\Factory\FactoryInterface;

final class CustomPublisherHeaderListener
{
    public const PUBLISHER_HEADER = 'X-Superdesk-Publisher';

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var FactoryInterface
     */
    private $versionFactory;

    public function __construct(bool $debug, FactoryInterface $versionFactory)
    {
        $this->debug = $debug;
        $this->versionFactory = $versionFactory;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $value = true;
        if ($this->debug) {
            $version = $this->versionFactory->create();
            $value = $version->getVersion();
        }
        $response->headers->add([self::PUBLISHER_HEADER => $value]);
    }
}
