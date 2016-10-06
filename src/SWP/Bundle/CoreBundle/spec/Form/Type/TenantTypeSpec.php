<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Form\Type\OrganizationCodeChoiceType;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Bundle\CoreBundle\Form\Type\ThemeNameChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @mixin TenantType
 */
class TenantTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantType::class);
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType(FormTypeInterface::class);
    }

    public function it_should_build_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'description' => 'Tenant name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('subdomain', TextType::class, [
                'required' => true,
                'description' => 'Tenant subdomain',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('themeName', ThemeNameChoiceType::class, [
                'required' => false,
                'description' => 'Tenant theme name',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('organization', OrganizationCodeChoiceType::class, [
                'required' => false,
                'description' => 'Tenant organization code',
            ])
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
        $this->getBlockPrefix()->shouldReturn('tenant');
    }
}
