<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

class PrefixCandidatesSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates');
    }

    public function it_should_get_tenant_aware_prefixes(TenantAwarePathBuilderInterface $pathBuilder)
    {
        $pathsNames = ['routes', 'custom'];
        $this->setRoutePathsNames($pathsNames);
        $pathBuilder->build($pathsNames)->willReturn(['/swp/default/routes', '/swp/default/custom']);
        $this->setPathBuilder($pathBuilder);
        $this->getPrefixes()->shouldReturn(['/swp/default/routes', '/swp/default/custom']);
    }
}
