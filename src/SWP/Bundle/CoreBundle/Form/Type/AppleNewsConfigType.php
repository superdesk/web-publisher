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

use SWP\Bundle\CoreBundle\Model\AppleNewsConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AppleNewsConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channelId', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 3]),
                ],
            ])
            ->add('apiKeyId', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 3]),
                ],
            ])
            ->add('apiKeySecret', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 3]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => AppleNewsConfig::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
