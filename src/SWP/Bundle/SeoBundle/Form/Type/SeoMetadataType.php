<?php

declare(strict_types=1);

namespace SWP\Bundle\SeoBundle\Form\Type;

use SWP\Component\Seo\Model\SeoMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
