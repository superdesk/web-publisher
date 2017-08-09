<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Checker;

use SWP\Bundle\CoreBundle\Enhancer\RouteEnhancer;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Takeit\Bundle\AmpHtmlBundle\Checker\AmpSupportCheckerInterface;

final class AmpSupportChecker implements AmpSupportCheckerInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param TenantContextInterface $tenantContext
     * @param RequestStack           $requestStack
     */
    public function __construct(TenantContextInterface $tenantContext, RequestStack $requestStack)
    {
        $this->tenantContext = $tenantContext;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();
        $request = $this->requestStack->getCurrentRequest();

        return (null !== $request->attributes->get(RouteEnhancer::ARTICLE_META, null) || 'swp_package_preview' === $request->attributes->get('_route')) && $tenant->isAmpEnabled();
    }
}
