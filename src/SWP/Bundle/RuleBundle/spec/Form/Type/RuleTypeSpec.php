<?php

/*
 * This file is part of the Superdesk Web Publisher Rule Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\RuleBundle\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\Form\Type\UnstructuredType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @mixin RuleType
 */
final class RuleTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RuleType::class);
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    public function it_should_build_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('expression', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('priority', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('configuration', KeyValueType::class, [
                'value_type' => UnstructuredType::class,
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('description', TextType::class)
            ->willReturn($builder)
        ;

        $builder
            ->add('name', TextType::class)
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
        $this->getBlockPrefix()->shouldReturn('rule');
    }
}
