<?php

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentListBundle\Form\Type;

use Prophecy\Argument;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListType;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentListBundle\Form\Type\ContentListTypeSelectorType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ContentListType
 */
final class ContentListTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListType::class);
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    public function it_has_a_block_prefix()
    {
        $this->getBlockPrefix()->shouldReturn('');
    }

    public function it_configures_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    public function it_build_a_form(FormBuilderInterface $builder, FormBuilderInterface $builderFilters)
    {
        $builder
            ->add('name', TextType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('type', ContentListTypeSelectorType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('description', TextType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('filters', TextType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('limit', IntegerType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('cacheLifeTime', IntegerType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->get('filters')
            ->shouldBeCalled()
            ->willReturn($builderFilters)
        ;

        $builderFilters->addModelTransformer(Argument::type(CallbackTransformer::class))
            ->willReturn($builderFilters);

        $builderFilters->addViewTransformer(Argument::type(CallbackTransformer::class))
            ->willReturn($builderFilters);

        $this->buildForm($builder, []);
    }
}
