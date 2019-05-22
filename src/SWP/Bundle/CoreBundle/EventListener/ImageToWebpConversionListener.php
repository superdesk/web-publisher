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
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class ImageToWebpConversionListener
{
    protected $imageConversionProducer;

    protected $tenantContext;

    public function __construct(ProducerInterface $imageConversionProducer, TenantContextInterface $tenantContext)
    {
        $this->imageConversionProducer = $imageConversionProducer;
        $this->tenantContext = $tenantContext;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $rendition = $args->getEntity();
        if (!$rendition instanceof ImageRenditionInterface) {
            return;
        }

        $this->imageConversionProducer->publish(serialize([
            'rendition' => $rendition,
            'tenant' => $this->tenantContext->getTenant(),
        ]));
    }
}
