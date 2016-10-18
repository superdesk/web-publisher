<?php

namespace SWP\Bundle\MenuBundle\Form\Type;

use SWP\Bundle\ContentBundle\Form\Type\RouteSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('label')
            ->add('uri')
            ->add('parent', MenuItemSelectorType::class)
            ->add('route', RouteSelectorType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function getBlockPrefix()
    {
        return 'menu';
    }
}
