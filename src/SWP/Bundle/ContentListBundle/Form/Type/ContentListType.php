<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'description' => 'List name',
            ])
            ->add('type', ContentListTypeSelectorType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'description' => 'List type',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'description' => 'List description',
            ])
            ->add('limit', IntegerType::class, [
                'required' => false,
                'description' => 'List limit',
            ])
            ->add('cacheLifeTime', IntegerType::class, [
                'required' => false,
                'description' => 'List cache life time',
            ])
            ->add('filters', TextType::class, [
                'required' => false,
                'description' => 'Content list filters in JSON format.',
            ])
        ;

        $builder->get('filters')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return json_encode($value);
                },
                function ($value) {
                    if (is_array($value)) {
                        return $value;
                    }

                    if (null !== $value && '' !== $value) {
                        return json_decode($value, true);
                    }

                    return [];
                }
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'content_list';
    }
}
