<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Form\Extension;

use SWP\Bundle\ContentBundle\Form\Type\RouteSelectorType;
use SWP\Bundle\RedirectRouteBundle\Form\RedirectRouteType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class RedirectRouteTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('routeSource', RouteSelectorType::class);
        $builder->add('routeTarget', RouteSelectorType::class);
    }

    public function getExtendedType(): string
    {
        return RedirectRouteType::class;
    }
}
