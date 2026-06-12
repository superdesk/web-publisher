<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A single key/value row used by KeyValueType.
 */
class KeyValueRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('key', $options['key_type'], $options['key_options'])
            ->add('value', $options['value_type'], $options['value_options']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key_type' => TextType::class,
            'key_options' => [],
            'value_type' => TextType::class,
            'value_options' => [],
        ]);
    }
}
