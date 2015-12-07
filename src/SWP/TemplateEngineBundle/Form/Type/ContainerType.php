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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'required' => false,
        ));
        $builder->add('height', null, array(
            'required' => false,
        ));
        $builder->add('width', null, array(
            'required' => false,
        ));
        $builder->add('styles', null, array(
            'required' => false,
        ));
        $builder->add('visible', ChoiceType::class, array(
            'choices'  => array(
                '1' => true,
                '0' => false,
            ),
            'choices_as_values' => false,
        ));
        $builder->add('cssClass', null, array(
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'container';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false
        ));
    }
}
