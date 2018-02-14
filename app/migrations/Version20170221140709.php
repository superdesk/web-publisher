<?php

namespace SWP\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SWP\Component\Common\Criteria\Criteria;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170221140709 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $query = $this->container->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT t FROM SWP\Bundle\CoreBundle\Model\Tenant t');
        $tenants = $query->getResult();
        $containerRepository = $this->container->get('swp.repository.container');
        $revisionRepository = $this->container->get('swp.repository.revision');
        foreach ($tenants as $tenant) {
            $criteria = new Criteria();
            $criteria->set('revision', null);
            $criteria->set('tenantCode', $tenant->getCode());
            $containersWithoutRevision = $containerRepository->getQueryByCriteria($criteria, [], 'c')
                ->getQuery()
                ->getResult();

            $tenantPublishedRevision = $revisionRepository->getPublishedRevision()
                ->andWhere('r.tenantCode = :tenantCode')
                ->setParameter('tenantCode', $tenant->getCode())
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $tenantPublishedRevision) {
                continue;
            }

            foreach ($containersWithoutRevision as $container) {
                $container->setRevision($tenantPublishedRevision);
            }
            $this->container->get('doctrine')->getManager()->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }
}
