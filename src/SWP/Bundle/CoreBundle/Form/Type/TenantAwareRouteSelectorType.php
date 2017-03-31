<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Form\DataTransformer\TenantAwareRouteToIdTransformer;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TenantAwareRouteSelectorType extends AbstractType
{
    private $tenantContext;

    private $tenantRepository;

    public function __construct(TenantContextInterface $tenantContext, RouteRepositoryInterface $routeRepository)
    {
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $routeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new TenantAwareRouteToIdTransformer($this->tenantContext, $this->tenantRepository));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected route does not exist for given tenant!',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
