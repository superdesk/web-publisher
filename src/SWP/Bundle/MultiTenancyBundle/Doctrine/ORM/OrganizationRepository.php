<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace SWP\Bundle\MultiTenancyBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;

class OrganizationRepository extends EntityRepository implements OrganizationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.name = :name')
            ->andWhere('o.enabled = true')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.code = :code')
            ->andWhere('o.enabled = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailable()
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.enabled = true')
            ->getQuery()
            ->getArrayResult();
    }
}
