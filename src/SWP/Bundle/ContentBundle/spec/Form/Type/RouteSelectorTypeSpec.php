<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Form\Type;

use SWP\Bundle\ContentBundle\Form\DataTransformer\RouteToIdTransformer;
use SWP\Bundle\ContentBundle\Form\Type\RouteSelectorType;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin RouteSelectorType
 */
final class RouteSelectorTypeSpec extends ObjectBehavior
{
    public function let(RouteProviderInterface $routeProvider)
    {
        $this->beConstructedWith($routeProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteSelectorType::class);
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    public function it_should_build_form(FormBuilderInterface $builder, RouteProviderInterface $routeProvider)
    {
        $builder
            ->addModelTransformer(
                new RouteToIdTransformer($routeProvider->getWrappedObject())
            )->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    public function it_should_set_defaults(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['invalid_message' => 'The selected route does not exist!'])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    public function it_should_have_a_parent()
    {
        $this->getParent()->shouldReturn(TextType::class);
    }
}
