<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Form\Type;

use SWP\Bundle\MultiTenancyBundle\Form\DataTransformer\TenantToCodeTransformer;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TenantChoiceType extends AbstractType
{
    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * TenantChoiceType constructor.
     *
     * @param TenantRepositoryInterface $tenantRepository
     * @param TenantContextInterface    $tenantContext
     */
    public function __construct(TenantRepositoryInterface $tenantRepository, TenantContextInterface $tenantContext)
    {
        $this->tenantRepository = $tenantRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new TenantToCodeTransformer($this->tenantRepository, $this->tenantContext));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'swp_tenant';
    }
}
