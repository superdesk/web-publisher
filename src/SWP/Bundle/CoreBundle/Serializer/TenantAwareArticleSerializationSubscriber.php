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

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class TenantAwareArticleSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var TenantInterface
     */
    private $originalTenant;

    /**
     * TenantAwareArticleSerializationSubscriber constructor.
     *
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext, TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'class' => Article::class,
                'method' => 'onPostSerialize',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'class' => Article::class,
                'method' => 'onPreSerialize',
            ],
        ];
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPreSerialize(ObjectEvent $event)
    {
        $data = $event->getObject();
        if ($this->tenantContext->getTenant()->getCode() !== $data->getTenantCode()) {
            $this->originalTenant = $this->tenantContext->getTenant();
            $tenant = $this->tenantRepository->findOneByCode($data->getTenantCode());
            $this->tenantContext->setTenant($tenant);
        }
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        if ($this->originalTenant && $this->tenantContext->getTenant() !== $this->originalTenant) {
            $this->tenantContext->setTenant($this->originalTenant);
        }
    }
}
