<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RouteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                   new NotBlank(),
                   new Length(['min' => 1]),
                ],
            ])
            ->add('type', TextType::class, [
                'required' => true,
                'constraints' => [
                   new NotBlank(),
                   new Length(['min' => 1]),
                ],
            ])
            ->add('parent', TextType::class, [
                'required' => false,
                'constraints' => [
                   new Length(['min' => 1]),
                ],
            ])
            ->add('content', TextType::class, [
                'required' => false,
                'constraints' => [
                   new Length(['min' => 1]),
                ],
                'description' => 'Relative content path. e.g.: /test-content-article',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getName()
    {
        return 'route';
    }
}
