<?php

namespace spec\SWP\MultiTenancyBundle\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\ContentBundle\Document\Route;
use SWP\MultiTenancyBundle\Doctrine\PHPCR\TenantAwarePathBuilderInterface;

class TenantAwareRouterSpec extends ObjectBehavior
{
    public function let(
        TenantAwarePathBuilderInterface $pathBuilder,
        $requestContext,
        $nestedMatcher,
        $generator,
    ) {
        $requestContext->beADoubleOf('Symfony\Component\Routing\RequestContext');
        $nestedMatcher->beADoubleOf('Symfony\Cmf\Component\Routing\NestedMatcher\NestedMatcher');
        $generator->beADoubleOf('Symfony\Cmf\Component\Routing\ContentAwareGenerator');

        $this->beConstructedWith($requestContext, $nestedMatcher, $generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\Routing\TenantAwareRouter');
    }

    function it_should_generate_tenant_aware_route($pathBuilder, $generator, Route $route)
    {
        $name = '/articles/features';
        $this->setPathBuilder($pathBuilder)->shouldBeNull();
        /*$generator->generate('/swp/default/routes/articles/features')
            ->shouldBeCalled()
            ->willReturn($route);*/

        $pathBuilder->build('articles/features')
            ->shouldBeCalled()
            ->willReturn('/swp/default/routes/articles/features');

        $this->generate($name)->shouldReturn('http://example.com/articles/features');
    }

    function it_should_set_path_builder($pathBuilder)
    {
        $this->setPathBuilder($pathBuilder)->shouldBeNull();
    }
}
