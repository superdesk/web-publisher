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

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\OutputChannelBundle\Form\Type\OutputChannelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TenantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->add('subdomain', TextType::class, [
                'required' => false,
                'help' => 'Tenant subdomain',
                'constraints' => [
                    new Length(['min' => 3]),
                ],
            ])
            ->add('domainName', TextType::class, [
                'required' => true,
                'help' => 'Tenant domain name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('themeName', ThemeNameChoiceType::class, [
                'required' => false,
                'help' => 'Tenant theme name',
            ])
            ->add('organization', OrganizationCodeChoiceType::class, [
                'required' => false,
                'help' => 'Tenant organization code',
            ])
            ->add('ampEnabled', BooleanType::class, [
                'required' => false,
                'help' => 'Defines whether Google AMP HTML support is enabled or not (true or false).',
            ])
            ->add('fbiaEnabled', BooleanType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Defines whether Facebook Instant Articles support is enabled or not (true or false).',
            ])
            ->add('paywallEnabled', BooleanType::class, [
                'mapped' => false,
                'required' => false,
                'help' => 'Defines whether Paywall support is enabled or not (true or false).',
            ])
            ->add('outputChannel', OutputChannelType::class, [
                'required' => false,
                'help' => 'Output Channel',
            ])
            ->add('defaultLanguage', TextType::class, [
                'required' => false,
                'help' => 'Default language',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
