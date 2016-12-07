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

namespace spec\SWP\Bundle\CoreBundle\Checker;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Checker\AmpSupportChecker;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Takeit\Bundle\AmpHtmlBundle\Checker\AmpSupportCheckerInterface;

final class AmpSupportCheckerSpec extends ObjectBehavior
{
    public function let(TenantContextInterface $tenantContext, RequestStack $requestStack)
    {
        $this->beConstructedWith($tenantContext, $requestStack);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AmpSupportChecker::class);
        $this->shouldImplement(AmpSupportCheckerInterface::class);
    }

    public function it_checks_if_amp_html_support_is_enabled(
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        RequestStack $requestStack,
        Request $request
    ) {
        $parameterBag = new ParameterBag();
        $parameterBag->set('_article_meta', new \stdClass());
        $request->attributes = $parameterBag;

        $requestStack->getCurrentRequest()->willReturn($request);
        $tenant->isAmpEnabled()->willReturn(true);
        $tenantContext->getTenant()->willReturn($tenant);

        $this->isEnabled()->shouldReturn(true);
    }

    public function it_checks_if_amp_html_support_is_disabled(
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        RequestStack $requestStack,
        Request $request,
        ParameterBag $parameterBag
    ) {
        $request->attributes = $parameterBag;
        $requestStack->getCurrentRequest()->willReturn($request);

        $tenant->isAmpEnabled()->willReturn(false);
        $tenantContext->getTenant()->willReturn($tenant);

        $this->isEnabled()->shouldReturn(false);
    }
}
