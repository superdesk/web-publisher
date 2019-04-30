<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Seo Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SeoBundle\Form\Type;

use SWP\Component\Seo\Model\SeoMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

final class SeoMetadataType extends AbstractType
{
    public const MAX_LIMIT = 200;

    private $dataClass;

    public function __construct(?string $dataClass = null)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metaDescription', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('metaTitle', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('ogDescription', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('ogTitle', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('twitterDescription', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('twitterTitle', TextType::class, [
                'constraints' => new Length(['max' => self::MAX_LIMIT]),
            ])
            ->add('metaMediaFile', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ],
            ])
            ->add('ogMediaFile', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ],
            ])
            ->add('twitterMediaFile', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => $this->dataClass ?? SeoMetadata::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
