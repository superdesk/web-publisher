<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\OutputChannelBundle\Form\Type;

use SWP\Bundle\CoreBundle\Model\OutputChannel;
use SWP\Component\OutputChannel\Model\OutputChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OutputChannelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Wordpress' => OutputChannelInterface::TYPE_WORDPRESS,
                    'PWA' => OutputChannelInterface::TYPE_PWA,
                ],
            ])
        ;

        $formModifier = function (FormInterface $form, ?string $type) {
            if (OutputChannelInterface::TYPE_WORDPRESS === $type) {
                $form->add('config', WordpressOutputChannelConfigType::class);
            }
            if (OutputChannelInterface::TYPE_PWA === $type) {
                $form->add('config', WordpressOutputChannelConfigType::class);
            }
        };

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                if (null !== $event->getData()) {
                    $formModifier($event->getForm(), $data->getType());
                }
            }
        );

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $type = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $type);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => OutputChannel::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'swp_output_channel';
    }
}
