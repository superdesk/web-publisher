<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Factory;

use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * Class TenantFactory.
 */
class TenantFactory implements TenantFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    protected $decoratedFactory;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var OrganizationRepositoryInterface
     */
    protected $organizationRepository;

    /**
     * TenantFactory constructor.
     *
     * @param FactoryInterface                $decoratedFactory
     * @param GeneratorInterface              $generator
     * @param OrganizationRepositoryInterface $organizationRepository
     */
    public function __construct(
        FactoryInterface $decoratedFactory,
        GeneratorInterface $generator,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->generator = $generator;
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->decoratedFactory->create();
        $tenant->setCode($this->generator->generate(6));

        return $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function createForOrganization($code)
    {
        if (null === $organization = $this->organizationRepository->findOneByCode($code)) {
            throw new \InvalidArgumentException(sprintf('Organization does not exist with code "%s".', $code));
        }

        /** @var TenantInterface $tenant */
        $tenant = $this->create();
        $tenant->setOrganization($organization);

        return $tenant;
    }
}
