<?php

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\MultiTenancyBundle\Form\Type\TenantChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class TenantPublishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tenant', TenantChoiceType::class)
            ->add('route', TenantAwareRouteSelectorType::class);
    }
}
