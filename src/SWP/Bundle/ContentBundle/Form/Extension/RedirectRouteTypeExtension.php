<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

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
