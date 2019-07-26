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
                'help' => 'List name',
            ])
            ->add('type', ContentListTypeSelectorType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'help' => 'List type',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'help' => 'List description',
            ])
            ->add('limit', IntegerType::class, [
                'required' => false,
                'help' => 'List limit',
            ])
            ->add('cacheLifeTime', IntegerType::class, [
                'required' => false,
                'help' => 'List cache life time',
            ])
            ->add('filters', TextType::class, [
                'required' => false,
                'help' => 'Content list filters in JSON format.',
            ])
        ;

        $builder->get('filters')
            ->addModelTransformer(new CallbackTransformer(
                static function ($value) {
                    $value = self::transformArrayKeys($value, 'camel');

                    return json_encode($value);
                },
                static function ($value) {
                    if (is_array($value)) {
                        return $value;
                    }

                    if (null !== $value && '' !== $value) {
                        $value = json_decode($value, true);
                        if (is_array($value)) {
                            return $value;
                        }
                    }

                    return [];
                }
            ))
        ->addViewTransformer(new CallbackTransformer(
            static function ($value) {
                if (is_array($value)) {
                    return json_encode(self::transformArrayKeys($value, 'snake'));
                }

                if (null !== $value && '' !== $value) {
                    $value = json_decode($value, true);
                    if (is_array($value)) {
                        return json_encode(self::transformArrayKeys($value, 'snake'));
                    }
                }

                return json_encode([]);
            },
            static function ($value) {
                $value = json_decode($value, true);
                if (is_array($value)) {
                    $value = self::transformArrayKeys($value, 'camel');
                }

                return json_encode($value);
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
        return '';
    }

    public static function snakeToCamel(string $str): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    public static function camelToSnake(string $str): string
    {
        $str = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $str);

        return  strtolower($str);
    }

    public static function transformArrayKeys(array $data, string $outputCase): array
    {
        foreach ($data as $key => $item) {
            $newKey = null;
            if ('camel' === $outputCase) {
                $data[$newKey = self::snakeToCamel($key)] = $item;
            } elseif ('snake' === $outputCase) {
                $data[$newKey = self::camelToSnake($key)] = $item;
            }

            if ($newKey !== $key) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
