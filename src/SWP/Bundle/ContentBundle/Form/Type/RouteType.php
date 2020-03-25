<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use SWP\Bundle\StorageBundle\Form\Type\UnstructuredType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form Type for Routes.
 */
class RouteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
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
            ->add('slug', TextType::class, [
                'required' => false,
                'constraints' => [
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
            ->add('templateName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 1]),
                ],
            ])
            ->add('articlesTemplateName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 1]),
                ],
            ])
            ->add('content', ArticleSelectorType::class, [
                'required' => false,
                'help' => 'Content identifier (e.g. article identifier)',
            ])
            ->add('parent', RouteSelectorType::class)
            ->add('cacheTimeInSeconds', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(0),
                ],
            ])
            ->add('variablePattern', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 1]),
                ],
            ])
            ->add('requirements', KeyValueType::class, [
                'required' => false,
                'value_type' => UnstructuredType::class,
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                ],
                'help' => 'Position under parent subtree in which to place the route.',
            ]);

        $builder->get('cacheTimeInSeconds')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return $value;
                },
                function ($value) {
                    return (int) $value;
                }
            ))
        ;
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
