<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentListFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('publishedBefore', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('publishedAfter', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('route', TextType::class)
            ->add('author', TextType::class)
            ->add('metadata', TextType::class)
        ;

        $builder->get('metadata')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return json_encode($value);
                },
                function ($value) {
                    if (is_array($value)) {
                        return $value;
                    }

                    if (null !== $value && '' !== $value) {
                        return json_decode($value, true);
                    }

                    return [];
                }
            ))
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'content_list_filters';
    }
}
