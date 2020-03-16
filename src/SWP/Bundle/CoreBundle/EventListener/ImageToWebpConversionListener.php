<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\CoreBundle\MessageHandler\Message\ConvertImageMessage;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

class ImageToWebpConversionListener
{
    protected $messageBus;

    protected $tenantContext;

    protected $isWebpConversionEnabled;

    protected $eventDispatcher;

    public function __construct(MessageBusInterface $messageBus, TenantContextInterface $tenantContext, string $isWebpConversionEnabled, EventDispatcherInterface $eventDispatcher)
    {
        $this->messageBus = $messageBus;
        $this->tenantContext = $tenantContext;
        $this->isWebpConversionEnabled = $isWebpConversionEnabled;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $rendition = $args->getObject();
        if (!$this->isWebpConversionEnabled || !$rendition instanceof ImageRenditionInterface) {
            return;
        }

        $tenantId = $this->tenantContext->getTenant()->getId();

        $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function (TerminateEvent $event) use ($rendition, $tenantId) {
            $this->messageBus->dispatch(new ConvertImageMessage(
                (int) $rendition->getImage()->getId(),
                (int) $tenantId
            ));
        });
    }
}
