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
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Events as PackageEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ImageToWebpConversionListener
{
    protected $imageConversionProducer;

    protected $tenantContext;

    protected $isWebpConversionEnabled;

    protected $eventDispatcher;

    public function __construct(ProducerInterface $imageConversionProducer, TenantContextInterface $tenantContext, string $isWebpConversionEnabled, EventDispatcherInterface $eventDispatcher)
    {
        $this->imageConversionProducer = $imageConversionProducer;
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

        $this->eventDispatcher->addListener(PackageEvents::PACKAGE_PROCESSED, function (GenericEvent $event) use ($rendition, $tenantId) {
            $this->imageConversionProducer->publish(serialize([
                'image' => $rendition->getImage(),
                'tenantId' => $tenantId,
            ]));
        });
    }
}
