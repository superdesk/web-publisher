<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Plan Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PlanBundle\Form\Type;

use SWP\Component\Plan\Model\PlanInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('currency', TextType::class)
            ->add('interval', ChoiceType::class, [
                'choices' => [
                    'Day' => PlanInterface::INTERVAL_DAY,
                    'Month' => PlanInterface::INTERVAL_MONTH,
                    'Year' => PlanInterface::INTERVAL_YEAR,
                ],
            ])
            ->add('intervalCount', IntegerType::class)
            ->add('amount', IntegerType::class)
            //->add('enabled', TextType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'swp_plan';
    }
}
