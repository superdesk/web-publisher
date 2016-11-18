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
use SWP\Bundle\ContentListBundle\Form\Type\ContentListItemType;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ContentListItemType
 */
final class ContentListItemTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListItemType::class);
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    public function it_has_a_block_prefix()
    {
        $this->getBlockPrefix()->shouldReturn('content_list_item');
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

    public function it_build_a_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('sticky', CheckboxType::class, Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }
}
