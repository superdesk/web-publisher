<?php

/**
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
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @mixin RouteToIdTransformer
 */
final class RouteToIdTransformerSpec extends ObjectBehavior
{
    function let(RouteProviderInterface $routeProvider)
    {
        $this->beConstructedWith($routeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteToIdTransformer::class);
    }

    function it_implements_an_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_should_return_empty_string_if_null_transformed()
    {
        $this->transform(null)->shouldReturn('');
    }
}
