<?php

declare(strict_types=1);

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

    private $tenantContext;

    /**
     * TenantChoiceType constructor.
     *
     * @param TenantRepositoryInterface $tenantRepository
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
