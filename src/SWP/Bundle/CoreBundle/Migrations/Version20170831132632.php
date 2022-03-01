<?php

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170831132632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_ec6095fe5e237e06');
        $this->addSql('DROP INDEX host_idx');
        $this->addSql('CREATE UNIQUE INDEX host_idx ON swp_tenant (domain_name, subdomain, deleted_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX host_idx');
        $this->addSql('CREATE UNIQUE INDEX uniq_ec6095fe5e237e06 ON swp_tenant (name)');
        $this->addSql('CREATE UNIQUE INDEX host_idx ON swp_tenant (domain_name, subdomain)');
    }
}
