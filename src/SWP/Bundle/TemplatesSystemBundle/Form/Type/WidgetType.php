<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', null, [
                'required' => false,
            ])
            ->add('visible', TextType::class, [
                'description' => 'Defines whether widget is visible or not (true or false).',
            ])
            ->add('parameters', TextType::class, [
                'required' => false,
            ]);

        $builder->get('visible')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return $value;
                },
                function ($value) {
                    if (is_bool($value) || in_array($value, ['true', 'false', '1', '0', null])) {
                        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }

                    throw new TransformationFailedException('Wrong boolean value passed.');
                }
            ));

        $builder->get('parameters')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    if (is_array($value) && !empty($value)) {
                        return json_encode($value);
                    }

                    return $value;
                },
                function ($value) {
                    if (is_string($value)) {
                        return json_decode($value, true);
                    }

                    if (null === $value) {
                        return [];
                    }

                    return $value;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getBlockPrefix()
    {
        return 'widget';
    }
}
