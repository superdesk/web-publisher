<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170301172303 extends AbstractMigration implements ContainerAwareInterface
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
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_tenant ALTER subdomain DROP NOT NULL');
        $query = $this->container->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT t.id, t.domainName, t.subdomain FROM SWP\Bundle\CoreBundle\Model\Tenant t');
        $tenants = $query->getResult();
        $domain = $this->container->getParameter('env(SWP_DOMAIN)');
        /** @var TenantInterface $tenant */
        foreach ($tenants as $tenant) {
            $date = new \DateTime();
            if (null === $tenant->getDomainName()) {
                $this->addSql('UPDATE swp_tenant SET domain_name = ?, updated_at = ? WHERE id = ?', [$domain, $date->format('Y-m-d H:i:s'), $tenant->getId()]);
            }
            if ('default' === $tenant->getSubdomain()) {
                $this->addSql('UPDATE swp_tenant SET subdomain = ?, updated_at = ? WHERE id = ?', [null, $date->format('Y-m-d H:i:s'), $tenant->getId()]);
            }
        }
        $this->container->get('doctrine')->getManager()->flush();

        $this->addSql('DROP INDEX uniq_ec6095fec1d5962e');
        $this->addSql('CREATE UNIQUE INDEX host_idx ON swp_tenant (domain_name, subdomain)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $tenants = $this->container->get('swp.repository.tenant')->findAll();
        /** @var TenantInterface $tenant */
        foreach ($tenants as $tenant) {
            if (null === $tenant->getSubdomain()) {
                $tenant->setSubdomain('default');
            }
        }
        $this->container->get('doctrine')->getManager()->flush();

        $this->addSql('ALTER TABLE swp_tenant ALTER subdomain SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_ec6095fec1d5962e ON swp_tenant (subdomain)');
        $this->addSql('DROP INDEX host_idx');
    }
}
