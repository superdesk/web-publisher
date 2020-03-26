<?php

namespace SWP\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
