<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180307144819 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_publish_destination_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_publish_destination (id INT NOT NULL, tenant_id INT DEFAULT NULL, route_id INT DEFAULT NULL, organization_id INT NOT NULL, fbia BOOLEAN NOT NULL, published BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C71004A9033212A ON swp_publish_destination (tenant_id)');
        $this->addSql('CREATE INDEX IDX_8C71004A34ECB4E6 ON swp_publish_destination (route_id)');
        $this->addSql('CREATE INDEX IDX_8C71004A32C8A3DE ON swp_publish_destination (organization_id)');
        $this->addSql('ALTER TABLE swp_publish_destination ADD CONSTRAINT FK_8C71004A9033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_publish_destination ADD CONSTRAINT FK_8C71004A34ECB4E6 FOREIGN KEY (route_id) REFERENCES swp_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE swp_publish_destination ADD CONSTRAINT FK_8C71004A32C8A3DE FOREIGN KEY (organization_id) REFERENCES swp_organization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_publish_destination_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_publish_destination');
    }
}
