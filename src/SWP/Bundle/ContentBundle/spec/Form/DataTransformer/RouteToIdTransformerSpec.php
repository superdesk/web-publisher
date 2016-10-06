<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Form\DataTransformer;

use SWP\Bundle\ContentBundle\Form\DataTransformer\RouteToIdTransformer;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @mixin RouteToIdTransformer
 */
final class RouteToIdTransformerSpec extends ObjectBehavior
{
    public function let(RouteProviderInterface $routeProvider)
    {
        $this->beConstructedWith($routeProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteToIdTransformer::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_should_return_null_if_null_transformed()
    {
        $this->transform(null)->shouldReturn(null);
    }

    public function it_should_throw_an_exception_when_not_route()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringTransform(new \stdClass())
        ;
    }

    public function it_should_transform_route_to_id(RouteInterface $route)
    {
        $route->getId()->willReturn('/some/path/id');

        $this->transform($route)->shouldReturn('/some/path/id');
    }

    public function it_should_return_null_if_null_reverse_transformed()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    public function it_should_throw_an_exception_during_reverse_transform()
    {
        $this
            ->shouldThrow(TransformationFailedException::class)
            ->duringReverseTransform('')
        ;
    }

    public function it_should_reverse_transform_id_to_route(
        RouteProviderInterface $routeProvider,
        RouteInterface $route
    ) {
        $routeProvider->getOneById('some-id')->willReturn($route);

        $this->reverseTransform('some-id')->shouldReturn($route);
    }
}
