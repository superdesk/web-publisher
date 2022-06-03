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
use Symfony\Component\HttpKernel\Event\RequestEvent ;

class SecuredContentPushListener
{
    private const SUPERDESK_HEADER = 'x-superdesk-signature';

    private const PUBLISHER_HEADER = 'x-publisher-signature';

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
     * @param RequestEvent $event
     *
     * @throws AuthenticationException
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (
            'swp_api_content_push' !== $routeName &&
            'swp_api_core_add_extra_data' !== $routeName
        ) {
            return;
        }

        $signature = null;
        if ($request->headers->has(self::SUPERDESK_HEADER)) {
            $signature = $request->headers->get(self::SUPERDESK_HEADER);
        } elseif ($request->headers->has(self::PUBLISHER_HEADER)) {
            $signature = $request->headers->get(self::PUBLISHER_HEADER);
        }

        /** @var OrganizationInterface $organization */
        $organization = $this->tenantContext->getTenant()->getOrganization();
        $organizationToken = $organization->getSecretToken();
        if (null === $organizationToken && null === $signature) {
            return;
        }

        if (null === $organizationToken) {
            $event->setResponse(new Response('Bad credentials', 401));
            $event->stopPropagation();

            return;
        }

        $content = $request->getContent();
        $token = hash_hmac('sha1', $content, $organizationToken);
        if ($signature !== 'sha1='.$token) {
            $event->setResponse(new Response('Bad credentials', 401));
            $event->stopPropagation();
        }
    }
}
