<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExportAnalyticsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'required' => false,
                'help' => 'Export start date, e.g. 2015-01-01',
                'widget' => 'single_text',
                'input' => 'string',
            ])
            ->add('end', DateType::class, [
                'required' => false,
                'help' => 'Export end date, e.g. 2016-01-01',
                'widget' => 'single_text',
                'input' => 'string',
            ])
            ->add('routes', CollectionType::class, [
                'entry_type' => ExportAnalyticsRouteType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false,
                'help' => 'Routes ids',
            ])
            ->add('authors', CollectionType::class, [
                'entry_type' => ExportAnalyticsAuthorType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false,
                'help' => 'Authors ids',
            ])
            ->add('term', TextType::class, [
                'required' => false,
                'help' => 'Search phrase',
            ])
        ;
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
