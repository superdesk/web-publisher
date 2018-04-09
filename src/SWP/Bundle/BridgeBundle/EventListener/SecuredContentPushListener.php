<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\BridgeBundle\EventListener;

use Superdesk\ContentApiSdk\Exception\AuthenticationException;
use SWP\Bundle\CoreBundle\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SecuredContentPushListener
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * SecuredContentPushListener constructor.
     *
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws AuthenticationException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->headers->has('x-superdesk-signature')) {
            return;
        }

        /** @var OrganizationInterface $organization */
        $organization = $this->tenantContext->getTenant()->getOrganization();
        $token = hash_hmac('sha1', $request->getContent(), $organization->getSecretToken());
        if ($request->headers->get('x-superdesk-signature') !== 'sha1='.$token) {
            throw new AuthenticationException('Bad credentials');
        }
    }
}
