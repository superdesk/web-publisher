<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Form\Type;

use SWP\Bundle\TemplateEngineBundle\Form\DataTransformer\ContainerToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerBranchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', TextType::class, array(
                'invalid_message' => 'That is not a valid container id',
            ))
            ->add('target_name', TextType::class, array(
                'mapped' => false
            ));

        $em = $options['entity_manager'];
        $builder->get('source')
            ->addModelTransformer(new ContainerToIdTransformer($em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'SWP\Bundle\TemplateEngineBundle\Model\ContainerBranch',
        ]);

        $resolver->setRequired('entity_manager');
    }

    public function getName()
    {
        return 'containerBranch';
    }
}
