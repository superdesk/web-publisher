<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Form\Type;

use SWP\Bundle\ContentListBundle\Form\DataTransformer\StringToDateTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentListFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('publishedBefore', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('publishedAfter', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('route', TextType::class)
            ->add('author', TextType::class)
            ->add('metadata', TextType::class)
        ;

        $builder->get('publishedAt')
            ->addModelTransformer(new StringToDateTransformer())
        ;

        $builder->get('publishedBefore')
            ->addModelTransformer(new StringToDateTransformer())
        ;

        $builder->get('publishedAfter')
            ->addModelTransformer(new StringToDateTransformer())
        ;

        $builder->get('metadata')
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
            ))
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'content_list_filters';
    }
}
