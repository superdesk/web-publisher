<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class PublishDestinationProvider implements PublishDestinationProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $publishDestinationRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * PublishDestinationProvider constructor.
     *
     * @param RepositoryInterface    $publishDestinationRepository
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(RepositoryInterface $publishDestinationRepository, TenantContextInterface $tenantContext)
    {
        $this->publishDestinationRepository = $publishDestinationRepository;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestinations(PackageInterface $package): array
    {
        return $this->publishDestinationRepository->createQueryBuilder('pd')
            ->where('pd.packageGuid = :guid')
            ->leftJoin('pd.tenant', 't')
            ->leftJoin('pd.organization', 'o')
            ->leftJoin('pd.route', 'r')
            ->addSelect('t', 'o', 'r')
            ->setParameter('guid', $package->getEvolvedFrom() ?: $package->getGuid())
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countDestinations(PackageInterface $package): int
    {
        return (int) $this->publishDestinationRepository->createQueryBuilder('pd')
            ->select('count(pd)')
            ->where('pd.packageGuid = :guid')
            ->andWhere('pd.tenant = :tenant')
            ->setParameter('tenant', $this->tenantContext->getTenant())
            ->setParameter('guid', $package->getEvolvedFrom() ?: $package->getGuid())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
