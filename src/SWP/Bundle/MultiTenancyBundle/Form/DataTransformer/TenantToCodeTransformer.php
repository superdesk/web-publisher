<?php

declare(strict_types=1);

namespace SWP\Bundle\MultiTenancyBundle\Form\DataTransformer;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class TenantToCodeTransformer implements DataTransformerInterface
{
    private $tenantRepository;
    private $tenantContext;

    public function __construct(TenantRepositoryInterface $tenantRepository, TenantContextInterface $tenantContext)
    {
        $this->tenantRepository = $tenantRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof TenantInterface) {
            throw new UnexpectedTypeException($value, TenantInterface::class);
        }

        return $value->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        $tenant = $this->tenantRepository->findOneByCode($value);

        if (null === $tenant) {
            throw new TransformationFailedException(sprintf(
                'Tenant with identifier "%s" equals "%s" does not exist.',
                'code',
                $value
            ));
        }

        $this->tenantContext->setTenant($tenant);

        return $tenant;
    }
}
