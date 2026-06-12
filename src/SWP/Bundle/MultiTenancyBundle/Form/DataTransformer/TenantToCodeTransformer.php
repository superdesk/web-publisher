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

namespace SWP\Bundle\MultiTenancyBundle\Form\DataTransformer;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class TenantToCodeTransformer implements DataTransformerInterface
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
     * TenantToCodeTransformer constructor.
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
    public function transform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof TenantInterface) {
            throw new UnexpectedTypeException($value, TenantInterface::class);
        }

        return $value->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
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
