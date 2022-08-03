<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180409113543 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_output_channel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_output_channel (id INT NOT NULL, tenant_id INT DEFAULT NULL, config TEXT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AFE4D08F9033212A ON swp_output_channel (tenant_id)');
        $this->addSql('COMMENT ON COLUMN swp_output_channel.config IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE swp_output_channel ADD CONSTRAINT FK_AFE4D08F9033212A FOREIGN KEY (tenant_id) REFERENCES swp_tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_output_channel_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_output_channel');
    }
}
