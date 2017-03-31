<?php

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\ContentBundle\Form\Type\RouteSelectorType;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\Form\Type\TenantChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TenantPublishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tenant', TenantChoiceType::class)
            ->add('route', TenantAwareRouteSelectorType::class);
    }
}
