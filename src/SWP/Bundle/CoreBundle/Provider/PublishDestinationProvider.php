<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

final class PublishDestinationProvider implements PublishDestinationProviderInterface
{
    private $publishDestinationRepository;

    private $tenantContext;

    public function __construct(RepositoryInterface $publishDestinationRepository, TenantContextInterface $tenantContext)
    {
        $this->publishDestinationRepository = $publishDestinationRepository;
        $this->tenantContext = $tenantContext;
    }

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
