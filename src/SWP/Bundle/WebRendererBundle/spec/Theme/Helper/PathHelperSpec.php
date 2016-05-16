<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\WebRendererBundle\Theme\Helper;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Theme\Helper\PathHelper;
use SWP\Component\Common\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

/**
 * @mixin PathHelper
 */
class PathHelperSpec extends ObjectBehavior
{
    function let(TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($tenantContext);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(PathHelper::class);
    }
    function it_should_apply_suffix_to_paths(TenantContextInterface $tenantContext, TenantInterface $tenant)
    {
        $tenantContext->getTenant()->willReturn($tenant);
        $tenant->getSubdomain()->willReturn('subdomain');
        $paths = ['/path1/', '/path2/'];
        $this->applySuffixFor($paths)->shouldReturn(['/path1/subdomain/', '/path2/subdomain/']);
    }

    function it_should_return_an_empty_array()
    {
        $this->applySuffixFor([])->shouldEqual([]);
    }
}
