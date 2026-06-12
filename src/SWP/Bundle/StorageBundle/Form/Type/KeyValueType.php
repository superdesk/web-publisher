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
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Maps a hash (associative array) to a collection of editable key/value rows.
 *
 * Replacement for the abandoned burgov/key-value-form-bundle KeyValueType.
 */
class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            static function ($hash) {
                if (null === $hash) {
                    return [];
                }

                if (!\is_array($hash)) {
                    throw new TransformationFailedException('Expected an array.');
                }

                $rows = [];
                foreach ($hash as $key => $value) {
                    $rows[] = ['key' => $key, 'value' => $value];
                }

                return $rows;
            },
            static function ($rows) {
                if (null === $rows) {
                    return [];
                }

                $hash = [];
                foreach ($rows as $row) {
                    if (!\is_array($row) || !\array_key_exists('key', $row)) {
                        continue;
                    }
                    if (\array_key_exists($row['key'], $hash)) {
                        throw new TransformationFailedException(sprintf('Duplicate key "%s".', $row['key']));
                    }

                    $hash[$row['key']] = $row['value'] ?? null;
                }

                return $hash;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => KeyValueRowType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'key_type' => TextType::class,
            'key_options' => [],
            'value_type' => TextType::class,
            'value_options' => [],
        ]);

        $resolver->setNormalizer('entry_options', static function ($options, $value) {
            return array_replace((array) $value, [
                'key_type' => $options['key_type'],
                'key_options' => $options['key_options'],
                'value_type' => $options['value_type'],
                'value_options' => $options['value_options'],
            ]);
        });
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
