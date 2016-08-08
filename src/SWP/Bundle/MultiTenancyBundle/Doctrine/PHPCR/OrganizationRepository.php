<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;

class OrganizationRepository extends DocumentRepository implements OrganizationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this->findOneBy([
            'name' => $name,
            'enabled' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this->findOneBy([
            'code' => $code,
            'enabled' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailable()
    {
        return $this
            ->createQueryBuilder('t')
                ->where()
                    ->eq()
                        ->field('t.enabled')->literal(true)
                    ->end()
                ->end()
            ->getQuery();
    }
}
