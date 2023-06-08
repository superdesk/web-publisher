<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\CoreBundle\Model\Settings;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608061408 extends AbstractMigration implements ContainerAwareInterface
{
    private ContainerInterface $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS swp_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    }

    public function postUp(Schema $schema): void
    {
        /**
         * @var EntityManagerInterface $entityManager
         */
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $query = $this->container->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT t.id, t.domainName, t.subdomain FROM SWP\Bundle\CoreBundle\Model\Tenant t');
        $tenants = $query->getResult();

        foreach ($tenants as $tenant) {

            /**
             * @var TenantInterface $tenant
             */
            $qb = $entityManager->createQueryBuilder();
            $setting = $qb->select('s')
                ->from(Settings::class, 's')
                ->andWhere('s.name = ?1')
                ->andWhere('s.owner = ?2')
                ->setParameter(1, 'override_slug_on_correction')
                ->setParameter(2, $tenant['id'])
                ->getQuery()
                ->getOneOrNullResult();

            if (!$setting) {
                $setting = new Settings();
                $setting->setName('override_slug_on_correction');
                $setting->setScope('tenant');
                $setting->setOwner($tenant['id']);
                $setting->setValue(true);
                $entityManager->persist($setting);
                $entityManager->flush();
                $entityManager->clear();
            }
        }
    }
}
