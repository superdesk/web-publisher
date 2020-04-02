<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\CoreBundle\Model\PublishDestination;
use SWP\Bundle\MultiTenancyBundle\Form\Type\TenantSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublishDestinationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tenant', TenantSelectorType::class)
            ->add('route', TenantAwareRouteSelectorType::class)
            ->add('isPublishedFbia', BooleanType::class)
            ->add('isPublishedToAppleNews', BooleanType::class)
            ->add('packageGuid', TextType::class)
            ->add('published', BooleanType::class)
            ->add('paywallSecured', BooleanType::class)
            ->add('contentLists', CollectionType::class, [
                'allow_add' => true,
                'entry_type' => ContentListPositionType::class,
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PublishDestination::class,
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
}
