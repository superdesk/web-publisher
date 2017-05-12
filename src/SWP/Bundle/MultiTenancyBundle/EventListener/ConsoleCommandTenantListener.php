<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\EventListener;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Class ConsoleCommandTenantListener.
 *
 * It set tenant from tenant code provided in console command options
 */
class ConsoleCommandTenantListener
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var TenantRepositoryInterface
     */
    protected $tenantRepository;

    /**
     * ConsoleCommandTenantListener constructor.
     *
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext, TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $tenantCode = $event->getInput()->getOption('tenant');
        if (null !== $tenantCode) {
            $tenant = $this->tenantRepository->findOneByCode($tenantCode);
            if (null !== $tenant) {
                $this->tenantContext->setTenant($tenant);
            }
        }
    }
}
