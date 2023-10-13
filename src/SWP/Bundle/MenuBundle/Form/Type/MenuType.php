<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle\Form\Type;

use SWP\Bundle\ContentBundle\Form\Type\RouteSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
                'help' => 'Menu item name',
            ])
            ->add('label', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'help' => 'Menu item label',
            ])
            ->add('uri', TextType::class, [
                'required' => false,
                'help' => 'Menu item URI',
            ])
            ->add('parent', MenuItemSelectorType::class, [
                'required' => false,
                'help' => 'Menu item identifier (e.g. 10)',
            ])
            ->add('route', RouteSelectorType::class, [
                'required' => false,
                'help' => 'Route identifier (e.g. 10)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
