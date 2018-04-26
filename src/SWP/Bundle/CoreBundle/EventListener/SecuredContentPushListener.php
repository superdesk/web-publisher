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

use Superdesk\ContentApiSdk\Exception\AuthenticationException;
use SWP\Bundle\CoreBundle\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\Response;
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
        $routeName = $request->attributes->get('_route');
        if ('swp_api_content_push' !== $routeName && 'swp_api_assets_push' !== $routeName) {
            return;
        }

        /** @var OrganizationInterface $organization */
        $organization = $this->tenantContext->getTenant()->getOrganization();
        $organizationToken = $organization->getSecretToken();
        if (null === $organizationToken && !$request->headers->has('x-superdesk-signature')) {
            return;
        }

        $token = hash_hmac('sha1', $request->getContent(), $organizationToken);
        if ($request->headers->get('x-superdesk-signature') !== 'sha1='.$token) {
            $event->setResponse(new Response('Bad credentials', 401));
            $event->stopPropagation();
        }
    }
}
