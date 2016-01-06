<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\TemplateEngineBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('visible', ChoiceType::class, [
                'choices'  => [true => '1', false => '0'],
                'choices_as_values' => true
            ])
            ->add('parameters', TextType::class)
            ->addModelTransformer(new CallbackTransformer(
                function ($originalDescription) {
                    if ($originalDescription && is_array($originalDescription->getParameters())) {
                         $originalDescription->setParameters(json_encode($originalDescription->getParameters()));
                    }

                    return $originalDescription;
                },
                function ($submittedDescription) {
                    if ($submittedDescription && is_string($submittedDescription->getParameters())) {
                        $submittedDescription->setParameters(json_decode($submittedDescription->getParameters(), true));
                    }

                    return $submittedDescription;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection'   => false]);
    }
}
