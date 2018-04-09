<?php

declare(strict_types=1);

namespace SWP\Bundle\OutputChannelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class WordpressOutputChannelConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('key', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('secret', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }
}
