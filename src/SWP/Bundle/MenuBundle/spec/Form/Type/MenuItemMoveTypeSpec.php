<?php

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MenuBundle\Form\Type;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MenuBundle\Form\Type\MenuItemMoveType;
use SWP\Bundle\MenuBundle\Form\Type\MenuItemSelectorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin MenuItemMoveType
 */
final class MenuItemMoveTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MenuItemMoveType::class);
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    public function it_should_build_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('parent', MenuItemSelectorType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('position', IntegerType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    public function it_should_set_defaults(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['csrf_protection' => false])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    public function it_should_have_a_name()
    {
        $this->getBlockPrefix()->shouldReturn('menu_move');
    }
}
