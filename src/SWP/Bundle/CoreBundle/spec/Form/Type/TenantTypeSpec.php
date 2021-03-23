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
use SWP\Bundle\CoreBundle\Form\Type\AppleNewsConfigType;
use SWP\Bundle\CoreBundle\Form\Type\BooleanType;
use SWP\Bundle\CoreBundle\Form\Type\OrganizationCodeChoiceType;
use SWP\Bundle\CoreBundle\Form\Type\PWAConfigType;
use SWP\Bundle\CoreBundle\Form\Type\TenantType;
use SWP\Bundle\CoreBundle\Form\Type\ThemeNameChoiceType;
use SWP\Bundle\OutputChannelBundle\Form\Type\OutputChannelType;
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
                'help' => 'Tenant name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('subdomain', TextType::class, [
                'required' => false,
                'help' => 'Tenant subdomain',
                'constraints' => [
                    new Length(['min' => 3]),
                ],
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('domainName', TextType::class, [
                'required' => true,
                'help' => 'Tenant domain name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])->willReturn($builder)
        ;

        $builder
            ->add('themeName', ThemeNameChoiceType::class, [
                'required' => false,
                'help' => 'Tenant theme name',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('organization', OrganizationCodeChoiceType::class, [
                'required' => false,
                'help' => 'Tenant organization code',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('ampEnabled', BooleanType::class, [
                'required' => false,
                'help' => 'Defines whether Google AMP HTML support is enabled or not (true or false).',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('fbiaEnabled', BooleanType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Defines whether Facebook Instant Articles support is enabled or not (true or false).',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('paywallEnabled', BooleanType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Defines whether Paywall support is enabled or not (true or false).',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('outputChannel', OutputChannelType::class, [
                'required' => false,
                'help' => 'Output Channel',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('defaultLanguage', TextType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Default language',
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('appleNewsConfig', AppleNewsConfigType::class, [
                'required' => false,
                'help' => 'Apple News Config',
            ])
            ->willReturn($builder)
        ;

        $builder->add('pwaConfig', PWAConfigType::class, [
            'required' => false,
            'help' => 'Defines PWA config).',
        ])->willReturn($builder)
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
        $this->getBlockPrefix()->shouldReturn('');
    }
}
