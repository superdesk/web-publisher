<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Form\Type;

use SWP\Component\Seo\Model\SeoMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoMetadataType extends AbstractType
{
    private $dataClass;

    public function __construct(?string $dataClass = null)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metaDescription', TextType::class)
            ->add('metaTitle', TextType::class)
            ->add('ogDescription', TextType::class)
            ->add('ogTitle', TextType::class)
            ->add('twitterDescription', TextType::class)
            ->add('twitterTitle', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => $this->dataClass ?? SeoMetadata::class
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
