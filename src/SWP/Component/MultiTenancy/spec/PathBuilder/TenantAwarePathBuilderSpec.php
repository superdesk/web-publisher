<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\MultiTenancy\PathBuilder;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\Tenant;

class TenantAwarePathBuilderSpec extends ObjectBehavior
{
    public function let(TenantContextInterface $tenantContext)
    {
        $currentTenant = new Tenant();
        $currentTenant->setName('Default');
        $currentTenant->setSubdomain('default');
        $tenantContext->getTenant()->willReturn($currentTenant);

        $this->beConstructedWith($tenantContext, '/swp');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilder');
    }

    public function it_implements_path_builder_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface');
    }

    public function it_should_build_tenant_aware_path()
    {
        $this->build('routes/articles')->shouldReturn('/swp/default/routes/articles');
        $this->build('/routes/articles')->shouldReturn('/swp/default');
        $this->build('routes')->shouldReturn('/swp/default/routes');
        $this->build('/')->shouldReturn('/swp/default');
        $this->build('routes', 'context')->shouldReturn('/swp/context/routes');
    }

    public function it_should_throw_an_exception_when_no_tenant_and_empty_path($tenantContext)
    {
        $tenantContext->getTenant()->willReturn(new Tenant());

        $this->shouldThrow('PHPCR\RepositoryException')
                ->duringBuild('', 'test');

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild('');

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild(null);
    }

    public function it_should_throw_exception_when_tenant_present_and_empty_path($tenantContext)
    {
        $tenantContext->getTenant()->willReturn(new Tenant());

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild('', 'test');

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild('');

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild(null);
    }

    public function it_should_not_throw_an_exception_when_tenant_present()
    {
        $this->shouldNotThrow('PHPCR\RepositoryException')
            ->duringBuild([]);
    }

    public function it_should_throw_an_exception_when_no_tenant_and_no_path_given($tenantContext)
    {
        $tenantContext->getTenant()->willReturn(new Tenant());

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild([]);
    }

    public function it_should_build_multiple_tenant_aware_paths()
    {
        $this->build(['routes', 'routes1'])->shouldReturn(['/swp/default/routes', '/swp/default/routes1']);
        $this->build(['routes'])->shouldReturn(['/swp/default/routes']);
        $this->build(['routes', 'routes1'], 'context')
            ->shouldReturn(['/swp/context/routes', '/swp/context/routes1']);

        $this->build([], 'context')
            ->shouldReturn([]);

        $this->build([])
            ->shouldReturn([]);
    }

    public function it_should_test_path_context()
    {
        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild('/articles', '');

        $this->shouldThrow('PHPCR\RepositoryException')
            ->duringBuild('articles', '');

        $this->build('articles', '@')->shouldReturn('/swp/@/articles');
        $this->build('articles', null)->shouldReturn('/swp/default/articles');
        $this->build('/articles', null)->shouldReturn('/swp/default');
    }
}
